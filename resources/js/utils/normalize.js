export default {
    /**
     *
     * @param phoneNumber {string}
     * @returns {string}
     */
    phone(phoneNumber) {
        return phoneNumber.replace(/\D/g, '').replace(/^8/, 7);
    },
    /**
     *
     * @param name {string}
     * @returns {string}
     */
    name(name) {
        name = name.trim();
        return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
    }
}
