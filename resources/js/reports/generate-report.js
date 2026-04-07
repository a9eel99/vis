#!/usr/bin/env node
const puppeteer = require('puppeteer');
const fs        = require('fs');
const path      = require('path');

const args = {};
process.argv.slice(2).forEach(arg => {
    const [key, val] = arg.replace('--', '').split('=');
    args[key] = val;
});

if (!args.data || !args.output) {
    console.error(JSON.stringify({ error: 'Missing --data or --output argument' }));
    process.exit(1);
}

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

template = template.replace(
    '/* __REPORT_DATA_PLACEHOLDER__ */',
    `window.__REPORT_DATA__ = ${JSON.stringify(data)};`
);

const tmpHtml = outputPath.replace('.pdf', '__tmp.html');
fs.writeFileSync(tmpHtml, template, 'utf8');

(async () => {
    const browserlessToken = process.env.BROWSERLESS_TOKEN;
    let browser;

    if (browserlessToken) {
        browser = await puppeteer.connect({
            browserWSEndpoint: `wss://chrome.browserless.io?token=${browserlessToken}`,
        });
    } else {
        const execPath = process.env.PUPPETEER_EXECUTABLE_PATH || undefined;
        browser = await puppeteer.launch({
            headless: 'new',
            executablePath: execPath,
            args: ['--no-sandbox','--disable-setuid-sandbox','--disable-dev-shm-usage','--disable-gpu'],
        });
    }

    let page;
    try {
        page = await browser.newPage();

        // Block external requests
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

        if (browserlessToken) {
            // Use setContent for remote browser — load then wait for JS to render
            const htmlContent = fs.readFileSync(tmpHtml, 'utf8');
            await page.setContent(htmlContent, {
                waitUntil: 'load',
                timeout:   60000,
            });
        } else {
            await page.goto(`file://${tmpHtml}`, {
                waitUntil: 'load',
                timeout:   60000,
            });
        }

        // Wait for report JS to finish rendering
        await page.waitForFunction(
            () => document.getElementById('report-root') && document.getElementById('report-root').children.length > 0,
            { timeout: 15000 }
        ).catch(() => {}); // ignore if element not found

        await new Promise(r => setTimeout(r, 1500));

        await page.pdf({
            path:                outputPath,
            format:              'A4',
            printBackground:     true,
            displayHeaderFooter: true,
            headerTemplate:      '<span></span>',
            footerTemplate:      '<div style="width:100%;background:#0f172a;padding:5px 20px;display:flex;justify-content:space-between;align-items:center;font-family:Tahoma,Arial,sans-serif;font-size:8px;color:rgba(255,255,255,.35);box-sizing:border-box;"><span style="font-weight:800;color:rgba(255,255,255,.6)">autoscore</span><span class="title"></span><span>&#x627;&#x644;&#x635;&#x641;&#x62d;&#x629; <span class="pageNumber"></span> &#x645;&#x646; <span class="totalPages"></span></span></div>',
            margin: { top: '0mm', bottom: '22px', left: '0mm', right: '0mm' },
        });

        if (fs.existsSync(tmpHtml)) fs.unlinkSync(tmpHtml);
        console.log(JSON.stringify({ success: true, output: outputPath }));
        process.exit(0);

    } catch (err) {
        try { await browser.close(); } catch(e) {}
        if (fs.existsSync(tmpHtml)) fs.unlinkSync(tmpHtml);
        console.error(JSON.stringify({ error: err.message }));
        process.exit(1);
    }
})();