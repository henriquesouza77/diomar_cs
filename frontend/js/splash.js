/**
 * splash.js — Abertura animada na tela de login (renderizada ao vivo, sem vídeo)
 *
 * Linha do tempo (segundos):
 *   1  logo se forma: letras entram em cascata, a casinha gira, surge "IMÓVEIS"
 *   2  letras recolhem, a casinha vai ao centro e dá zoom "por dentro"
 *   3  a tela fica branca e se dissolve revelando o login
 * Roda de forma síncrona logo depois do markup do overlay (#splash),
 * para a tela de login nunca aparecer antes da abertura.
 */
'use strict';

(() => {
  /* ======================================================================
     CONFIG — ajuste tempos (s) e comportamento aqui
     ====================================================================== */
  const ONLY_ONCE_PER_SESSION = false;   // true: só na 1ª visita de cada sessão do navegador
  const KEY = 'diomar_splash_seen';

  const T = {
    letterStart: 0.15, letterStagger: 0.06, letterDur: 0.5,
    symbolStart: 0.30, symbolDur: 0.85,
    taglineStart: 0.80, taglineDur: 0.50,
    collapseStart: 1.30, collapseStagger: 0.05, collapseDur: 0.40,
    taglineOut: 1.30, taglineOutDur: 0.30,
    centerStart: 1.55, centerDur: 0.40,
    zoomStart: 1.75, zoomEnd: 2.45,
    revealStart: 2.45, revealEnd: 3.05,
  };
  const ZOOM_ROTATE_DEG = -10;
  const ORIGIN = { x: 50, y: 38 };      // ponto do símbolo (em %) onde o zoom entra: parede branca da casa

  /* ====================================================================== */
  const root = document.getElementById('splash');
  if (!root) return;

  const html = document.documentElement;
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const forced = /[?&]splash=1\b/.test(location.search);
  let seen = false;
  try { seen = ONLY_ONCE_PER_SESSION && sessionStorage.getItem(KEY) === '1'; } catch (e) { /* storage bloqueado */ }

  if ((reduced || seen) && !forced) { root.remove(); return; }

  html.classList.add('splash-on');
  document.body.style.overflow = 'hidden';

  /* ---------- Easing ---------- */
  function bezier(x1, y1, x2, y2) {
    const cx = 3 * x1, bx = 3 * (x2 - x1) - cx, ax = 1 - cx - bx;
    const cy = 3 * y1, by = 3 * (y2 - y1) - cy, ay = 1 - cy - by;
    const sx = t => ((ax * t + bx) * t + cx) * t;
    const sy = t => ((ay * t + by) * t + cy) * t;
    const dx = t => (3 * ax * t + 2 * bx) * t + cx;
    return x => {
      if (x <= 0) return 0; if (x >= 1) return 1;
      let t = x;
      for (let i = 0; i < 8; i++) {
        const e = sx(t) - x; if (Math.abs(e) < 1e-6) break;
        const d = dx(t); if (Math.abs(d) < 1e-6) break; t -= e / d;
      }
      return sy(t);
    };
  }
  const easeOut = bezier(0.16, 1, 0.3, 1);
  const easeInOut = bezier(0.65, 0, 0.35, 1);
  const easeIn = bezier(0.7, 0, 0.95, 0.45);
  const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
  const prog = (t, start, dur) => clamp((t - start) / dur);
  const lerp = (a, b, p) => a + (b - a) * p;

  /* ---------- Elementos ---------- */
  const lettersEl = root.querySelector('.splash__letters');
  const taglineEl = root.querySelector('.splash__tagline');
  const symbolEl = root.querySelector('.splash__symbol');
  const skipBtn = root.querySelector('[data-splash-skip]');

  const letters = [...lettersEl.dataset.text].map(ch => {
    const s = document.createElement('span');
    s.textContent = ch;
    lettersEl.appendChild(s);
    return s;
  });

  /* ---------- Layout (responsivo) ---------- */
  let L = null;
  function measure() {
    const vw = window.innerWidth, vh = window.innerHeight;
    const fs = Math.min(vw * 0.17, vh * 0.17, 150);
    const symS = fs * 1.15;
    const gap = fs * 0.18;

    lettersEl.style.fontSize = fs + 'px';
    taglineEl.style.fontSize = Math.max(11, fs * 0.22) + 'px';
    symbolEl.style.width = symbolEl.style.height = symS + 'px';

    const textW = lettersEl.offsetWidth;
    const left = (vw - (textW + gap + symS)) / 2;
    const top = vh / 2 - fs / 2 - fs * 0.1;

    lettersEl.style.left = left + 'px';
    lettersEl.style.top = top + 'px';
    symbolEl.style.left = left + textW + gap + 'px';
    symbolEl.style.top = vh / 2 - symS / 2 - fs * 0.1 + 'px';
    taglineEl.style.top = top + fs + fs * 0.14 + 'px';

    L = {
      vw, vh, symS,
      symCx: left + textW + gap + symS / 2,
      letterCenters: letters.map(s => left + s.offsetLeft + s.offsetWidth / 2),
      // zoom suficiente para a parede branca cobrir a tela inteira
      maxScale: Math.max(vh / (0.3 * symS), vw / (0.6 * symS)) * 1.25,
    };
  }

  /* ---------- Quadro da animação em t segundos ---------- */
  function render(t) {
    if (!L) measure();
    const N = letters.length;

    letters.forEach((el, i) => {
      const pin = easeOut(prog(t, T.letterStart + i * T.letterStagger, T.letterDur));
      const pout = easeInOut(prog(t, T.collapseStart + (N - 1 - i) * T.collapseStagger, T.collapseDur));
      const pull = (L.symCx - L.letterCenters[i]) * 0.55 * pout;
      el.style.opacity = pin * (1 - pout);
      el.style.transform = `translate(${pull}px, ${lerp(46, 0, pin)}px) scale(${lerp(1, 0.6, pout)})`;
    });

    const tin = easeOut(prog(t, T.taglineStart, T.taglineDur));
    const tout = easeInOut(prog(t, T.taglineOut, T.taglineOutDur));
    taglineEl.style.opacity = tin * (1 - tout);
    taglineEl.style.transform = `translateY(${lerp(18, 0, tin)}px)`;

    const sin = easeOut(prog(t, T.symbolStart, T.symbolDur));
    const cen = easeInOut(prog(t, T.centerStart, T.centerDur));
    const zp = easeIn(prog(t, T.zoomStart, T.zoomEnd - T.zoomStart));
    const scale = lerp(0.7, 1, sin) * Math.pow(L.maxScale, zp);
    const rot = lerp(-18, 0, sin) + ZOOM_ROTATE_DEG * zp;   // giro leve na entrada
    // Entrada gira em torno do centro; só no zoom a origem vai para a parede branca
    // (neste ponto rotação=0 e escala=1, então a troca não dá salto)
    symbolEl.style.transformOrigin = t < T.zoomStart ? '50% 50%' : `${ORIGIN.x}% ${ORIGIN.y}%`;
    symbolEl.style.opacity = clamp(sin * 2.2);
    symbolEl.style.transform =
      `translateX(${(L.vw / 2 - L.symCx) * cen}px) rotate(${rot}deg) scale(${scale})`;

    // Dissolve final: a tela branca some e revela o login
    root.style.opacity = 1 - easeInOut(prog(t, T.revealStart, T.revealEnd - T.revealStart));
  }

  /* ---------- Controle ---------- */
  let rafId = null, startedAt = null, done = false, released = false;

  // O formulário começa a entrar junto com a dissolução
  function releaseForm() {
    if (released) return;
    released = true;
    html.classList.remove('splash-on');
  }

  function finish(fast) {
    if (done) return;
    done = true;
    cancelAnimationFrame(rafId);
    try { sessionStorage.setItem(KEY, '1'); } catch (e) { /* ignora */ }
    releaseForm();
    document.body.style.overflow = '';
    window.removeEventListener('resize', measure);
    if (fast) {
      root.classList.add('is-leaving');
      setTimeout(() => root.remove(), 600);
    } else {
      root.remove();
    }
  }

  function frame(now) {
    if (startedAt === null) startedAt = now;
    const t = (now - startedAt) / 1000;
    render(t);
    if (t >= T.revealStart) releaseForm();
    if (t >= T.revealEnd) { finish(false); return; }
    rafId = requestAnimationFrame(frame);
  }

  skipBtn?.addEventListener('click', () => finish(true));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') finish(true); });
  window.addEventListener('resize', measure);

  // Espera as fontes (Montserrat) para medir as letras corretamente
  const ready = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
  Promise.race([ready, new Promise(r => setTimeout(r, 1200))]).then(() => {
    if (done) return;
    measure();
    render(0);
    rafId = requestAnimationFrame(frame);
  });
})();
