/**
 * charts.js — Wrapper de gráficos em Canvas puro
 * Sem dependências externas. Usado pelos relatórios do admin.
 */
'use strict';

const Charts = (() => {

  const palette = [
    '#1E3A8A', '#3B82F6', '#60A5FA', '#93C5FD',
    '#DBEAFE', '#10B981', '#F59E0B', '#EF4444',
  ];

  function getContext(canvas) {
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();

    canvas.width  = rect.width * dpr;
    canvas.height = rect.height * dpr;

    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);

    return { ctx, width: rect.width, height: rect.height };
  }

  // ============ BARRAS ============
  function bar(canvas, data, options = {}) {
    const { ctx, width, height } = getContext(canvas);
    const padding = { top: 24, right: 24, bottom: 40, left: 40 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;
    const maxValue = Math.max(...data.map(d => d.value), 1);

    ctx.clearRect(0, 0, width, height);

    // Grid
    ctx.strokeStyle = '#E2E8F0';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
      const y = padding.top + (chartH / 5) * i;
      ctx.beginPath();
      ctx.moveTo(padding.left, y);
      ctx.lineTo(width - padding.right, y);
      ctx.stroke();

      ctx.fillStyle = '#94A3B8';
      ctx.font = '11px Inter, sans-serif';
      ctx.textAlign = 'right';
      ctx.fillText(Math.round(maxValue * (1 - i / 5)), padding.left - 8, y + 4);
    }

    // Barras
    const barWidth = chartW / data.length * 0.6;
    const gap = chartW / data.length;

    data.forEach((item, i) => {
      const x = padding.left + gap * i + (gap - barWidth) / 2;
      const barH = (item.value / maxValue) * chartH;
      const y = padding.top + chartH - barH;

      const gradient = ctx.createLinearGradient(0, y, 0, padding.top + chartH);
      gradient.addColorStop(0, palette[i % palette.length]);
      gradient.addColorStop(1, palette[i % palette.length] + '80');

      ctx.fillStyle = gradient;
      ctx.beginPath();
      ctx.roundRect
        ? ctx.roundRect(x, y, barWidth, barH, [6, 6, 0, 0])
        : ctx.rect(x, y, barWidth, barH);
      ctx.fill();

      // Labels
      ctx.fillStyle = '#334155';
      ctx.font = '12px Inter, sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(item.label, x + barWidth / 2, height - 12);
    });
  }

  // ============ LINHA ============
  function line(canvas, data, options = {}) {
    const { ctx, width, height } = getContext(canvas);
    const padding = { top: 24, right: 24, bottom: 40, left: 40 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;
    const maxValue = Math.max(...data.map(d => d.value), 1);

    ctx.clearRect(0, 0, width, height);

    // Grid
    ctx.strokeStyle = '#E2E8F0';
    for (let i = 0; i <= 5; i++) {
      const y = padding.top + (chartH / 5) * i;
      ctx.beginPath();
      ctx.moveTo(padding.left, y);
      ctx.lineTo(width - padding.right, y);
      ctx.stroke();
    }

    // Pontos
    const points = data.map((d, i) => ({
      x: padding.left + (chartW / Math.max(data.length - 1, 1)) * i,
      y: padding.top + chartH - (d.value / maxValue) * chartH,
      value: d.value,
      label: d.label,
    }));

    // Área
    const areaGrad = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartH);
    areaGrad.addColorStop(0, 'rgba(59,130,246,0.3)');
    areaGrad.addColorStop(1, 'rgba(59,130,246,0)');

    ctx.fillStyle = areaGrad;
    ctx.beginPath();
    ctx.moveTo(points[0].x, padding.top + chartH);
    points.forEach(p => ctx.lineTo(p.x, p.y));
    ctx.lineTo(points[points.length - 1].x, padding.top + chartH);
    ctx.closePath();
    ctx.fill();

    // Linha
    ctx.strokeStyle = '#1E3A8A';
    ctx.lineWidth = 2.5;
    ctx.lineJoin = 'round';
    ctx.beginPath();
    points.forEach((p, i) => i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y));
    ctx.stroke();

    // Pontos coloridos
    points.forEach(p => {
      ctx.fillStyle = '#fff';
      ctx.beginPath();
      ctx.arc(p.x, p.y, 5, 0, Math.PI * 2);
      ctx.fill();
      ctx.strokeStyle = '#1E3A8A';
      ctx.lineWidth = 2.5;
      ctx.stroke();
    });

    // Labels
    ctx.fillStyle = '#334155';
    ctx.font = '12px Inter, sans-serif';
    ctx.textAlign = 'center';
    points.forEach(p => ctx.fillText(p.label, p.x, height - 12));
  }

  // ============ PIZZA / DONUT ============
  function pie(canvas, data, options = {}) {
    const { ctx, width, height } = getContext(canvas);
    const cx = width / 2;
    const cy = height / 2;
    const radius = Math.min(width, height) / 2 - 20;
    const total = data.reduce((s, d) => s + d.value, 0);

    if (!total) return;

    ctx.clearRect(0, 0, width, height);

    let startAngle = -Math.PI / 2;

    data.forEach((item, i) => {
      const sliceAngle = (item.value / total) * Math.PI * 2;
      ctx.beginPath();
      ctx.moveTo(cx, cy);
      ctx.arc(cx, cy, radius, startAngle, startAngle + sliceAngle);
      ctx.closePath();
      ctx.fillStyle = palette[i % palette.length];
      ctx.fill();

      startAngle += sliceAngle;
    });

    // Donut hole
    if (options.donut !== false) {
      ctx.beginPath();
      ctx.arc(cx, cy, radius * 0.55, 0, Math.PI * 2);
      ctx.fillStyle = '#fff';
      ctx.fill();

      // Center text
      ctx.fillStyle = '#0F172A';
      ctx.font = 'bold 24px Inter, sans-serif';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(total.toLocaleString('pt-BR'), cx, cy - 6);

      ctx.fillStyle = '#64748B';
      ctx.font = '12px Inter, sans-serif';
      ctx.fillText(options.centerLabel || 'Total', cx, cy + 14);
    }
  }

  // ============ DONUT ============
  function donut(canvas, data, options = {}) {
    return pie(canvas, data, { ...options, donut: true });
  }

  return { bar, line, pie, donut, palette };
})();

window.Charts = Charts;