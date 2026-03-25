/**
 * Jordanian License Plate Input
 * Format: XX - XXXXX (2 digits - space - 5 digits)
 * Or with letters: XX X XXXXX
 * Usage: initPlateInput('input-id')
 */

function initPlateInput(inputId) {
    var input = document.getElementById(inputId);
    if (!input) return;

    // Create styled wrapper
    var wrap = document.createElement('div');
    wrap.className = 'plate-wrap';
    wrap.style.cssText = 'position:relative;display:flex;align-items:center';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    // Add plate styling
    input.style.cssText += ';font-family:monospace;font-size:1.1rem;font-weight:700;letter-spacing:2px;text-align:center;direction:ltr';
    input.setAttribute('placeholder', '00 - 00000');
    input.setAttribute('maxlength', '11');
    input.setAttribute('dir', 'ltr');

    // Auto-format as user types
    input.addEventListener('input', function(e) {
        var val = this.value.replace(/[^0-9\u0600-\u06FF]/g, '');
        var formatted = '';

        for (var i = 0; i < val.length && i < 7; i++) {
            if (i === 2) formatted += ' - ';
            formatted += val[i];
        }

        this.value = formatted;
    });

    // Handle paste
    input.addEventListener('paste', function(e) {
        var self = this;
        setTimeout(function() {
            var val = self.value.replace(/[^0-9\u0600-\u06FF]/g, '');
            var formatted = '';
            for (var i = 0; i < val.length && i < 7; i++) {
                if (i === 2) formatted += ' - ';
                formatted += val[i];
            }
            self.value = formatted;
        }, 10);
    });
}