document.addEventListener('DOMContentLoaded', function() {
    var mVehicle = document.getElementById('m-vehicle');
    if (!mVehicle) return;
    mVehicle.addEventListener('change', function() {
        var lang = document.documentElement.getAttribute('lang') || 'en';
        var fuel = this.options[this.selectedIndex] ? this.options[this.selectedIndex].getAttribute('data-fuel') : null;
        var fuelInfo = document.getElementById('m-fuel-info');
        var autoLabel = document.getElementById('m-auto-label');
        var tSel = document.getElementById('m-template');
        autoLabel.style.display = 'none';
        fuelInfo.style.display = 'none';
        if (!fuel) return;
        var fuels = {ar:{gasoline:'بنزين',diesel:'ديزل',electric:'كهربائي',hybrid:'هجين',lpg:'غاز'},en:{gasoline:'Gasoline',diesel:'Diesel',electric:'Electric',hybrid:'Hybrid',lpg:'LPG'}};
        fuelInfo.textContent = (lang === 'ar' ? '⛽ نوع الوقود: ' : '⛽ Fuel: ') + ((fuels[lang] && fuels[lang][fuel]) || fuel);
        fuelInfo.style.display = 'block';
        var matched = false;
        for (var i = 0; i < tSel.options.length; i++) {
            if (tSel.options[i].getAttribute('data-fuel') === fuel) { tSel.selectedIndex = i; autoLabel.style.display = 'inline'; matched = true; break; }
        }
        if (!matched) {
            for (var i = 0; i < tSel.options.length; i++) { if (tSel.options[i].value && !tSel.options[i].getAttribute('data-fuel')) { tSel.selectedIndex = i; autoLabel.style.display = 'inline'; break; } }
        }
    });
});