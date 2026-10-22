import './bootstrap';
import QRCode from 'qrcode';

window.QRCode = QRCode;

function downloadQrCode(text, filename) {
    const canvas = document.createElement('canvas');

    window.QRCode.toCanvas(canvas, String(text), {
        width: 512,
        margin: 2,
        color: { dark: '#0f172a', light: '#ffffff' },
        errorCorrectionLevel: 'M',
    }, function (err) {
        if (err) {
            window.alert('Impossible de générer le QR code.');
            return;
        }

        const link = document.createElement('a');
        link.download = filename || 'qrcode.png';
        link.href = canvas.toDataURL('image/png');
        document.body.appendChild(link);
        link.click();
        link.remove();
    });
}

window.downloadQrCode = downloadQrCode;

document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-qr-download]');
    if (!trigger) {
        return;
    }

    event.preventDefault();
    downloadQrCode(
        trigger.getAttribute('data-code') || '',
        trigger.getAttribute('data-filename') || 'qrcode.png'
    );
});

function loadScript(src) {
    return new Promise(function (resolve, reject) {
        if (document.querySelector('script[src="' + src + '"]')) {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

document.addEventListener('click', async function (event) {
    const trigger = event.target.closest('[data-qr-download-all]');
    if (!trigger) {
        return;
    }

    event.preventDefault();

    try {
        await loadScript('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js');
    } catch (e) {
        window.alert('Impossible de charger le module de téléchargement groupé.');
        return;
    }

    if (!window.JSZip || !window.QRCode) {
        window.alert('Téléchargement groupé indisponible.');
        return;
    }

    const triggers = Array.from(document.querySelectorAll('[data-qr-download]'));
    if (!triggers.length) {
        window.alert('Aucun QR code à télécharger.');
        return;
    }

    const zip = new window.JSZip();

    for (const item of triggers) {
        const code = item.getAttribute('data-code') || '';
        const filename = item.getAttribute('data-filename') || ('billet-' + code + '.png');
        const canvas = document.createElement('canvas');

        await new Promise(function (resolve) {
            window.QRCode.toCanvas(canvas, String(code), {
                width: 512,
                margin: 2,
                color: { dark: '#0f172a', light: '#ffffff' },
                errorCorrectionLevel: 'M',
            }, function (err) {
                if (err) {
                    resolve();
                    return;
                }
                canvas.toBlob(function (blob) {
                    if (blob) {
                        zip.file(filename, blob);
                    }
                    resolve();
                });
            });
        });
    }

    const content = await zip.generateAsync({ type: 'blob' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(content);
    link.download = trigger.getAttribute('data-filename') || 'billets.zip';
    document.body.appendChild(link);
    link.click();
    link.remove();
});
