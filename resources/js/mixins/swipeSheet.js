// Закрытие нижних окон и полноэкранных диалогов свайпом вниз на телефоне.
// На карточке окна: data-swipe-close="<имя булевого флага в data>", :style="sheetStyle",
// @touchstart.passive="sheetTouchStart" @touchmove="sheetTouchMove" @touchend="sheetTouchEnd" @touchcancel="sheetTouchEnd".
// Прокручиваемая область внутри окна помечается классом .swipe-scroll: жест по ней при scrollTop > 0 — это скролл.
export default {
    data() {
        return {
            sheetDrag: {startY: null, dy: 0, close: null},
        };
    },
    computed: {
        isMobile() {
            return this.$vuetify.display.name === 'sm';
        },
        sheetStyle() {
            const dragging = this.sheetDrag.startY !== null;
            return {
                transform: this.sheetDrag.dy ? `translateY(${this.sheetDrag.dy}px)` : '',
                transition: dragging ? 'none' : 'transform .2s',
            };
        },
    },
    methods: {
        openDialog(name) {
            this.sheetDrag = {startY: null, dy: 0, close: null};
            this[name] = true;
        },
        sheetTouchStart(e) {
            const scroller = e.currentTarget.querySelector('.swipe-scroll');
            if (!this.isMobile || (scroller && scroller.contains(e.target) && scroller.scrollTop > 0)) {
                this.sheetDrag.startY = null;
                return;
            }
            this.sheetDrag.close = e.currentTarget.dataset.swipeClose;
            this.sheetDrag.startY = e.touches[0].clientY;
            this.sheetDrag.dy = 0;
        },
        sheetTouchMove(e) {
            if (this.sheetDrag.startY === null) return;
            const dy = e.touches[0].clientY - this.sheetDrag.startY;
            if (dy <= 0) {
                this.sheetDrag.dy = 0;
                return;
            }
            e.preventDefault(); // пока тянем окно вниз, список под пальцем не прокручиваем
            this.sheetDrag.dy = dy;
        },
        sheetTouchEnd() {
            if (this.sheetDrag.startY === null) return;
            const close = this.sheetDrag.dy > 80;
            this.sheetDrag.startY = null;
            // При закрытии смещение не сбрасываем: окно уезжает вниз анимацией Vuetify с текущего места
            if (close) {
                this[this.sheetDrag.close] = false;
            } else {
                this.sheetDrag.dy = 0;
            }
        },
    },
};
