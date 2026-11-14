/**
 * Копирование в буфер обмена с запасным способом для HTTP:
 * navigator.clipboard доступен только в защищённом контексте (HTTPS/localhost).
 * @param text {string}
 * @returns {Promise<boolean>}
 */
export async function copyText(text) {
    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(text);
            return true;
        }
    } catch (e) {
        // падаем в запасной способ
    }
    try {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(ta);
        return ok;
    } catch (e) {
        return false;
    }
}
