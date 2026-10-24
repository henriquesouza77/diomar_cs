// Renderiza index.html frame a frame (1080x1920) e monta o MP4 com ffmpeg.
// Uso: node render.mjs            -> ../diomar-splash.mp4
//      node render.mjs --frames   -> também deixa os PNGs em ./frames
import puppeteer from 'puppeteer-core';
import { spawn } from 'node:child_process';
import { mkdirSync, rmSync, existsSync } from 'node:fs';
import { pathToFileURL, fileURLToPath } from 'node:url';
import path from 'node:path';

const here = path.dirname(fileURLToPath(import.meta.url));
const OUT = path.resolve(here, '..', 'diomar-splash.mp4');
const FRAMES = path.join(here, 'frames');
const keepFrames = process.argv.includes('--frames');

const EDGE = [
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
  'C:/Program Files/Microsoft/Edge/Application/msedge.exe',
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
].find(existsSync);
if (!EDGE) throw new Error('Edge/Chrome não encontrado');

if (existsSync(FRAMES)) rmSync(FRAMES, { recursive: true });
mkdirSync(FRAMES);

const browser = await puppeteer.launch({ executablePath: EDGE, headless: 'new', args: ['--force-device-scale-factor=1', '--font-render-hinting=none'] });
const page = await browser.newPage();
await page.setViewport({ width: 1080, height: 1920, deviceScaleFactor: 1 });
await page.goto(pathToFileURL(path.join(here, 'index.html')).href, { waitUntil: 'networkidle0' });
await page.evaluate(() => window.ready);

const { fps, duration } = await page.evaluate(() => window.CONFIG);
const total = Math.round(fps * duration);

for (let i = 0; i < total; i++) {
  await page.evaluate(t => window.render(t), i / fps);
  await page.screenshot({ path: path.join(FRAMES, `f${String(i).padStart(4, '0')}.png`), type: 'png' });
}
await browser.close();

await new Promise((resolve, reject) => {
  const ff = spawn('ffmpeg', [
    '-y', '-framerate', String(fps), '-i', path.join(FRAMES, 'f%04d.png'),
    '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '17', '-pix_fmt', 'yuv420p', '-movflags', '+faststart', OUT,
  ], { stdio: 'inherit' });
  ff.on('exit', code => (code === 0 ? resolve() : reject(new Error('ffmpeg falhou: ' + code))));
});

if (!keepFrames) rmSync(FRAMES, { recursive: true });
console.log('OK ->', OUT, `(${total} frames)`);
