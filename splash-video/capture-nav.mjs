// Teste manual da barra de menu em vários tamanhos/estados.
// Uso: node capture-nav.mjs <pasta-de-saída>
import puppeteer from 'puppeteer-core';
import { existsSync, mkdirSync } from 'node:fs';
import path from 'node:path';

const out = process.argv[2];
mkdirSync(out, { recursive: true });
const EDGE = ['C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe', 'C:/Program Files/Microsoft/Edge/Application/msedge.exe'].find(existsSync);
const BASE = 'http://127.0.0.1:8091/diomar-cs/frontend/pages/';

const browser = await puppeteer.launch({ executablePath: EDGE, headless: 'new' });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', e => errors.push(e.message));

async function shot(name, url, w, h, after) {
  await page.setViewport({ width: w, height: h });
  await page.goto(BASE + url, { waitUntil: 'networkidle0' });
  await new Promise(r => setTimeout(r, 900));
  if (after) await after();
  const file = path.join(out, name + '.png');
  if (name === 'scrolled') await page.screenshot({ path: file });   // viewport inteiro: a barra é fixa
  else await page.screenshot({ path: file, clip: { x: 0, y: 0, width: w, height: Math.min(h, 420) } });
}

await shot('d1280', 'imoveis.php?purpose=aluguel', 1280, 800);
await shot('d1024', 'sobre.php', 1024, 800);
await shot('t820', 'index.php', 820, 800);
await shot('m390', 'contato.php', 390, 800);
await shot('m320', 'index.php', 320, 700);
await shot('m390open', 'contato.php', 390, 800, async () => {
  await page.click('#navbarToggle');
  await new Promise(r => setTimeout(r, 800));
});
// hover em item colapsado (dica) no desktop
await shot('hover', 'sobre.php', 1280, 800, async () => {
  const links = await page.$$('#navbar .navbar__link');
  await links[1].hover();
  await new Promise(r => setTimeout(r, 500));
});
// rolagem: estado .is-scrolled
await shot('scrolled', 'index.php', 1280, 800, async () => {
  await page.evaluate(() => window.scrollTo(0, 1200));
  await new Promise(r => setTimeout(r, 700));
});

console.log('erros de JS:', errors.length ? errors : 'nenhum');
await browser.close();
