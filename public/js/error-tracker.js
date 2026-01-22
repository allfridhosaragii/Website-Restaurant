(function () {
    'use strict';
    let html2canvasLoaded = false;
    function loadHtml2Canvas() {
        return new Promise((resolve, reject) => {
            if (typeof html2canvas !== 'undefined') {
                html2canvasLoaded = true;
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = () => {
                html2canvasLoaded = true;
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    function getBrowserInfo() {
        const ua = navigator.userAgent;
        let browser = 'Unknown';
        if (ua.includes('Firefox')) browser = 'Firefox';
        else if (ua.includes('Edg')) browser = 'Edge';
        else if (ua.includes('Chrome')) browser = 'Chrome';
        else if (ua.includes('Safari')) browser = 'Safari';
        else if (ua.includes('Opera')) browser = 'Opera';
        return browser;
    }
    function getDeviceType() {
        const ua = navigator.userAgent;
        if (/tablet|ipad|playbook|silk/i.test(ua)) return 'Tablet';
        if (/mobile|iphone|ipod|blackberry|opera mini|iemobile/i.test(ua)) return 'Mobile';
        return 'Desktop';
    }
    async function captureScreenshot() {
        if (!html2canvasLoaded) {
            try {
                await loadHtml2Canvas();
            } catch (e) {
                console.warn('Failed to load html2canvas');
                return null;
            }
        }
        try {
            const canvas = await html2canvas(document.body, {
                logging: false,
                useCORS: true,
                allowTaint: true,
                scale: 0.5, 
                width: window.innerWidth,
                height: Math.min(window.innerHeight, 1500) 
            });
            return canvas.toDataURL('image/png', 0.7); 
        } catch (e) {
            console.warn('Screenshot capture failed:', e);
            return null;
        }
    }
    async function sendErrorReport(errorData) {
        try {
            const screenshot = await captureScreenshot();
            const payload = {
                type: errorData.type || 'JavaScript Error',
                message: errorData.message,
                file: errorData.file,
                line: errorData.line,
                column: errorData.column,
                stack: errorData.stack,
                url: window.location.href,
                browser: getBrowserInfo(),
                deviceType: getDeviceType(),
                screenSize: `${window.innerWidth}x${window.innerHeight}`,
                screenshot: screenshot
            };
            await fetch('/api/error-report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
        } catch (e) {
            console.warn('Failed to send error report:', e);
        }
    }
    let lastError = '';
    let lastErrorTime = 0;
    function shouldReportError(message) {
        const now = Date.now();
        if (message === lastError && now - lastErrorTime < 5000) {
            return false; 
        }
        lastError = message;
        lastErrorTime = now;
        return true;
    }
    window.onerror = function (message, source, lineno, colno, error) {
        if (!shouldReportError(message)) return;
        sendErrorReport({
            type: 'JavaScript Error',
            message: message,
            file: source,
            line: lineno,
            column: colno,
            stack: error ? error.stack : null
        });
    };
    window.onunhandledrejection = function (event) {
        const message = event.reason ? (event.reason.message || String(event.reason)) : 'Unhandled Promise Rejection';
        if (!shouldReportError(message)) return;
        sendErrorReport({
            type: 'Unhandled Promise Rejection',
            message: message,
            stack: event.reason ? event.reason.stack : null
        });
    };
    setTimeout(loadHtml2Canvas, 3000);
    console.log('🔍 Error Tracker with Screenshot loaded');
})();