// Mirrors --ease-out in app.css: cubic-bezier(0.22, 1, 0.36, 1).
// JS cannot read the CSS token, so rAF loops solve x-for-t here.
// Keep these numbers in sync with the CSS curve by hand.
export const easeOutShared = (x) => {
    const solve = (t, a, b) => 3 * (1 - t) ** 2 * t * a + 3 * (1 - t) * t ** 2 * b + t ** 3;
    let low = 0;
    let high = 1;

    for (let i = 0; i < 12; i++) {
        const mid = (low + high) / 2;

        if (solve(mid, 0.22, 0.36) < x) {
            low = mid;
        } else {
            high = mid;
        }
    }

    return solve((low + high) / 2, 1, 1);
};
