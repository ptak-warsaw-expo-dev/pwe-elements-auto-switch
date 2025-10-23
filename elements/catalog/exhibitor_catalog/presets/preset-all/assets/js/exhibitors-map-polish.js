(function() {
    const _plRules = (typeof Intl !== "undefined" && Intl.PluralRules) ?
        new Intl.PluralRules("pl") :
        null;

    const NOUNS = {
        wyszukanie: { one: "Wyszukiwanie", few: "Wyszukiwania", many: "Wyszukiwań", other: "Wyszukiwań" },
    };

    function select(n) {
        if (_plRules) return _plRules.select(n);
        const abs = Math.abs(n);
        if (!Number.isInteger(abs)) return "other";
        const mod10 = abs % 10;
        const mod100 = abs % 100;
        if (abs === 1) return "one";
        if (mod10 >= 2 && mod10 <= 4 && !(mod100 >= 12 && mod100 <= 14)) return "few";
        if (abs === 0 || mod10 === 0 || mod10 >= 5 || (mod100 >= 12 && mod100 <= 14)) return "many";
        return "other";
    }

    function pl(nounKey, n) {
        const forms = NOUNS[nounKey];
        if (!forms) return "";
        const cat = select(n);
        return forms[cat] || forms.many || "";
    }

    function fmtCount(n, nounKey) { return `${n} ${pl(nounKey, n)}`; }

    function registerNouns(map) {
        if (map && typeof map === "object") Object.assign(NOUNS, map);
    }

    // Publiczne API
    window.PL = { select, pl, fmtCount, registerNouns, NOUNS };
})();