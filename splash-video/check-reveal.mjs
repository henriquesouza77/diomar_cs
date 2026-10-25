// Teste do fade-in/out por rolagem (falha com código 1 se quebrar). Uso: node check-reveal.mjs
// Precisa do site em http://127.0.0.1:8091/diomar-cs (veja a memória do projeto).
import assert from 'node:assert/strict';
import puppeteer from 'puppeteer-core';
import { existsSync } from 'node:fs';

const EDGE = ['C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe', 'C:/Program Files/Microsoft/Edge/Application/msedge.exe'].find(existsSync);
const browser = await puppeteer.launch({ executablePath: EDGE, headless: 'new' });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', e => errors.push(e.message));
await page.setViewport({ width: 1440, height: 900 });
await page.goto('http://127.0.0.1:8091/diomar-cs/frontend/pages/sobre.php', { waitUntil: 'networkidle0' });

const sel = '.about-section__content';
const wait = ms => new Promise(r => setTimeout(r, ms));
const state = () => page.$eval(sel, el => ({
  visible: el.classList.contains('is-visible'),
  top: Math.round(el.getBoundingClientRect().top),
  opacity: Number(getComputedStyle(el).opacity).toFixed(2),
}));

const y0 = await page.$eval(sel, el => el.getBoundingClientRect().top + scrollY);
// [descrição, rolagem, deve estar visível?]
const steps = [
  ['bloco no meio da tela',  y0 - 300, true],
  ['bloco bem acima (fora)', y0 + 1200, false],
  ['voltando: bloco no meio', y0 - 300, true],
];
for (const [nome, y, esperado] of steps) {
  await page.evaluate(v => window.scrollTo(0, v), y);
  await wait(1100);
  const st = await state();
  assert.equal(st.visible, esperado, `${nome}: esperava visible=${esperado}, veio ${JSON.stringify(st)}`);
  console.log('ok   ', nome);
}

await page.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
await wait(1200);
const escondidos = await page.evaluate(() => [...document.querySelectorAll('[data-reveal]')].filter(e => {
  const r = e.getBoundingClientRect();
  return r.top < innerHeight && r.bottom > 0 && !e.classList.contains('is-visible');
}).length);
assert.equal(escondidos, 0, 'no fim da página não pode sobrar bloco invisível na tela');
console.log('ok    fim da página sem bloco escondido');

assert.deepEqual(errors, [], 'erros de JS na página');
console.log('ok    sem erros de JS');
await browser.close();
