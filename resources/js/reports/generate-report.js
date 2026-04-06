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
    const browser = await puppeteer.launch({
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
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

        // Generate PDF
        await page.pdf({
            path:              outputPath,
            format:            'A4',
            printBackground:   true,
            displayHeaderFooter: false,
            margin: {
                top:    '0mm',
                bottom: '0mm',
                left:   '0mm',
                right:  '0mm',
            },
            preferCSSPageSize: true,
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