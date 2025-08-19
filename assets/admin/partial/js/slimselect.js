(function () {
    "use strict";

    // With search
    document.querySelectorAll(".slim-select").forEach(function (select) {
        new SlimSelect({ select: select });
    });

    // Without search
    document
        .querySelectorAll(".slim-select-no-search")
        .forEach(function (select) {
            new SlimSelect({
                select: select,
                settings: { searchable: false },
            });
        });
})();
