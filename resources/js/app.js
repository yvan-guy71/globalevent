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
