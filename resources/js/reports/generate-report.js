#!/usr/bin/env node
/**
 * VIS - Vehicle Inspection Report Generator
 * Uses Puppeteer (headless Chrome) for pixel-perfect PDF output
 *
 * Usage:
 *   node generate-report.js --data=<json_file> --output=<pdf_path> [--template=<html_file>]
 *
 * Install:
 *   npm install puppeteer
 */

const puppeteer = require('puppeteer');
const fs        = require('fs');
const path      = require('path');

// ─── Parse CLI args ──────────────────────────────────────────────────────────
const args = {};
process.argv.slice(2).forEach(arg => {
    const [key, val] = arg.replace('--', '').split('=');
    args[key] = val;
});

if (!args.data || !args.output) {
    console.error(JSON.stringify({ error: 'Missing --data or --output argument' }));
    process.exit(1);
}

// ─── Load data & template ────────────────────────────────────────────────────
const dataPath     = path.resolve(args.data);
const outputPath   = path.resolve(args.output);
const templatePath = args.template
    ? path.resolve(args.template)
    : path.join(__dirname, 'report-template.html');

if (!fs.existsSync(dataPath)) {
    console.error(JSON.stringify({ error: `Data file not found: ${dataPath}` }));
    process.exit(1);
}
if (!fs.existsSync(templatePath)) {
    console.error(JSON.stringify({ error: `Template not found: ${templatePath}` }));
    process.exit(1);
}

const data     = JSON.parse(fs.readFileSync(dataPath, 'utf8'));
let   template = fs.readFileSync(templatePath, 'utf8');

// ─── Inject JSON data into HTML ──────────────────────────────────────────────
// Replace the placeholder so the HTML page can access window.__REPORT_DATA__
template = template.replace(
    '/* __REPORT_DATA_PLACEHOLDER__ */',
    `window.__REPORT_DATA__ = ${JSON.stringify(data)};`
);

// Write temp HTML to disk (Puppeteer reads files better via file:// URI)
const tmpHtml = outputPath.replace('.pdf', '__tmp.html');
fs.writeFileSync(tmpHtml, template, 'utf8');

// ─── Generate PDF ─────────────────────────────────────────────────────────────
(async () => {
    const execPath = process.env.PUPPETEER_EXECUTABLE_PATH
        || '/home/u873439670/.cache/puppeteer/chrome/linux-121.0.6167.85/chrome-linux64/chrome'
        || undefined;

    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: execPath,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--font-render-hinting=none',
        ],
    });

    try {
        const page = await browser.newPage();

        // Block Google Fonts & external requests for speed
        await page.setRequestInterception(true);
        page.on('request', req => {
            const url = req.url();
            if (url.includes('googleapis.com') || url.includes('gstatic.com') ||
                url.includes('google.com') || req.resourceType() === 'media') {
                req.abort();
            } else {
                req.continue();
            }
        });

        // Load the HTML file
        await page.goto(`file://${tmpHtml}`, {
            waitUntil: 'networkidle0',
            timeout: 30000,
        });

        // Wait for fonts and charts to render
        await page.waitForFunction(() => document.readyState === 'complete');
        await page.evaluate(() => document.fonts.ready);

        // Small delay for SVG/canvas charts
        await new Promise(r => setTimeout(r, 800));

        // Inject report number into footer via meta tag
        const reportNum = await page.evaluate(() => {
            const d = window.__REPORT_DATA__;
            return d ? d.report_number : '';
        });

        // Generate PDF
        await page.pdf({
            path:              outputPath,
            format:            'A4',
            printBackground:   true,
            displayHeaderFooter: true,
            headerTemplate:    '<span></span>',
            footerTemplate:    `<div style="width:100%;background:#0f172a;padding:5px 20px;
                                 display:flex;justify-content:space-between;align-items:center;
                                 font-family:Tahoma,Arial,sans-serif;font-size:8px;
                                 color:rgba(255,255,255,.35);box-sizing:border-box;">
                                  <span style="font-weight:800;color:rgba(255,255,255,.6)">auto<span style="color:#f59e0b">score</span></span>
                                  <span class="title"></span>
                                  <span>الصفحة <span class="pageNumber"></span> من <span class="totalPages"></span></span>
                                </div>`,
            margin: { top:'0mm', bottom:'22px', left:'0mm', right:'0mm' },
        });

        await browser.close();

        // Cleanup temp file
        if (fs.existsSync(tmpHtml)) fs.unlinkSync(tmpHtml);

        console.log(JSON.stringify({ success: true, output: outputPath }));
        process.exit(0);

    } catch (err) {
        await browser.close();
        if (fs.existsSync(tmpHtml)) fs.unlinkSync(tmpHtml);
        console.error(JSON.stringify({ error: err.message }));
        process.exit(1);
    }
})();