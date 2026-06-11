import { SlotsManager } from "./slot-manager";

export function inputTimePickerComponent({
    // explicit parent access context
    dataStack
}) {
    const AMPM_AM = 'AM';
    const AMPM_PM = 'PM';


    return {
        /** @type {string} - Current hour value (00-23 or 01-12) */
        __hours: '',
        /** @type {string} - Current minute value (00-59) */
        __minutes: '',
        /** @type {string} - Current AM/PM toggle state */
        __ampm: AMPM_AM,

        /**
         * Handle a change to the hour segment.
         * Clamps to valid range, and triggers onHourFilled only on model-driven input (not keyboard).
         *
         * @param {string} val - Raw input value
         * @param {Object} [options]
         * @param {boolean} [options.byKeyboard=false] - Skip auto-advance if triggered by arrow keys
         */
        handleHour(val, { byKeyboard = false, deferCommit = false } = {}) {
            const max = dataStack.__is12Hour ? 12 : 23;
            const min = dataStack.__is12Hour ? 1 : 0;

            val = val.slice(-2);

            const num = parseInt(val, 10);

            if (!isNaN(num)) {
                if (num > max) val = String(max);
                if (num < min && val.length === 2) val = String(min).padStart(2, '0');
            }

            this.__hours = val;

            if (val.length === 2 && !byKeyboard) {
                this.focusEl(this.$refs.timeInputM);
                this.selectEl(this.$refs.timeInputM);
            }

            deferCommit || this.commitInputTime();
        },

        /**
         * Handle a change to the minute segment.
         * Clamps to 0–59, and triggers onMinuteFilled only on model-driven input (not keyboard).
         *
         * @param {string} val - Raw input value
         * @param {Object} [options]
         * @param {boolean} [options.byKeyboard=false] - Skip auto-advance if triggered by arrow keys
         */
        handleMinute(val, { byKeyboard = false, deferCommit = false } = {}) {
            val = val.slice(-2);

            const num = parseInt(val, 10);

            if (!isNaN(num) && num > 59) val = '59';

            this.__minutes = val;

            if (val.length === 2 && !byKeyboard) {
                if (dataStack.__is12Hour) {
                    this.focusEl(this.$refs.amPmToggler);
                }
            }

            // after the user enter the propriate minutes we need to commit the
            // value cs may the user keep the `AM` as period
            deferCommit || this.commitInputTime();
        },

        /**
         * Toggle between AM and PM periods.
         * Commits the updated time immediately.
         */
        toggleAmPm() {
            this.__ampm = this.__ampm === AMPM_AM ? AMPM_PM : AMPM_AM;
            this.commitInputTime();
        },

        /**
         * Clear all input fields and reset to initial state.
         * Clears hours, minutes, AM/PM, and invokes dataStack.clear().
         */
        clearInput() {
            this.__hours = '';
            this.__minutes = '';
            this.__ampm = AMPM_AM;
            dataStack?.clear();
        },

        /**
         * Fill input fields from a time state value.
         * Parses the state to extract minutes, converts to hours/minutes, applies 12-hour format if needed.
         *
         * @param {string} state - Time state value (expected format from SlotsManager)
         */
        fillTriggers(state) {

            let mins = SlotsManager.parse(state);

            if (mins === null) return;

            const h = Math.floor(mins / 60);
            const m = mins % 60;

            if (dataStack.__is12Hour) {
                this.handleHour(String(h % 12 || 12).padStart(2, '0'), { byKeyboard: true, deferCommit: true });
                this.__ampm = h >= 12 ? AMPM_PM : AMPM_AM;
            } else {
                this.handleHour(String(h).padStart(2, '0'), { byKeyboard: true, deferCommit: true });
            }

            this.handleMinute(String(m).padStart(2, '0'), { byKeyboard: true, deferCommit: true });

            this.commitInputTime();
        },

        /**
         * Commit the user-entered time to the parent dataStack.
         * Validates range constraints and disabled slots, converts 12-hour to 24-hour format if needed.
         * Sets dataStack.__state if valid, otherwise does nothing.
         */
        commitInputTime() {
            const h = parseInt(this.__hours, 10) || 0;
            const m = parseInt(this.__minutes, 10) || 0;

            if (m < 0 || m > 59) return;

            let hour = h;

            if (dataStack.__is12Hour) {
                if (h < 1 || h > 12) return;
                if (this.__ampm === AMPM_PM && h !== 12) hour = h + 12;
                else if (this.__ampm === AMPM_AM && h === 12) hour = 0;
            } else {
                if (h > 23) return;
            }

            const timeValue = `${String(hour).padStart(2, '0')}:${String(m).padStart(2, '0')}`;

            const isDisabled = dataStack.__slots.some(s => s.value === timeValue && s.disabled);

            if (isDisabled) return;

            // we need to the commit the value to the parent component
            //  so thier we can sync back to the state in the correct form
            dataStack.__state = timeValue;
        },

        /**
         * Focus an element using requestAnimationFrame.
         *
         * @param {HTMLElement} [el] - Element to focus
         */
        focusEl(el) {
            requestAnimationFrame(() => el?.focus());
        },
        /**
         * Select content in an element using requestAnimationFrame.
         *
         * @param {HTMLElement} [el] - Element to select content within
         */
        selectEl(el) {
            requestAnimationFrame(() => el?.select());
        },

        /**
         * Focus and select content in an element using requestAnimationFrame.
         *
         * @param {HTMLElement} [el] - Element to focus and select
         */
        focusAndSelectEl(el) {
            requestAnimationFrame(() => {
                el?.focus();
                el?.select();
            });
        },
        /**
         * Handle keyboard events for time input segments (hours/minutes).
         * Supports arrow keys for incrementing/decrementing values, Tab for navigation,
         * and special keys like Enter, Escape, and Backspace.
         *
         * @param {KeyboardEvent} event - Keyboard event
         * @param {string} segment - Segment identifier: 'h' for hours, 'm' for minutes
         */
        handleSegmentKey(event, segment) {
            const isHour = segment === 'h';
            const isMinute = segment === 'm';

            const input = event.target;

            const max = isHour ? (dataStack.__is12Hour ? 12 : 23) : 59;
            const min = isHour && dataStack.__is12Hour ? 1 : 0;

            let current = parseInt(
                isHour ? this.__hours : this.__minutes,
                10
            ) || 0;

            switch (event.key) {
                case 'ArrowUp':
                    event.preventDefault();
                    current = current >= max ? min : current + 1;

                    isHour ? this.handleHour(String(current).padStart(2, '0'), { byKeyboard: true })
                        : this.handleMinute(String(current).padStart(2, '0'), { byKeyboard: true });

                    this.selectEl(input);
                    break;

                case 'ArrowDown':
                    event.preventDefault();

                    current = current <= min ? max : current - 1;
                    isHour ? this.handleHour(String(current).padStart(2, '0'), { byKeyboard: true })
                        : this.handleMinute(String(current).padStart(2, '0'), { byKeyboard: true });

                    this.selectEl(input);
                    break;

                // left + right arrows: 
                // we need to go through : hours <-> minutes <-> AmPm toggler
                case 'ArrowRight':

                    if (isHour) {
                        this.focusAndSelectEl(this.$refs.timeInputM);
                    } else if (dataStack.__is12Hour) {
                        this.focusEl(this.$refs.amPmToggler);
                    }
                    break;

                case 'ArrowLeft':
                    if (isMinute) {
                        this.focusAndSelectEl(this.$refs.timeInputH);
                    }
                    break;

                case 'Tab':
                    event.preventDefault();

                    if (!event.shiftKey) {
                        if (isHour) this.focusEl(this.$refs.timeInputM);

                        else if (dataStack.__is12Hour) {
                            this.focusEl(this.$refs.amPmToggler)
                        }
                    } else {
                        if (!isHour) this.focusEl(this.$refs.timeInputH);
                    }

                    break;

                case 'Enter':
                    event.preventDefault();
                    this.commitInputTime();
                    break;

                case 'Escape':
                    dataStack?.close?.();
                    break;

                default:
                    if (!/^\d$/.test(event.key) &&
                        !['Backspace', 'Delete', 'Home', 'End'].includes(event.key)) {
                        event.preventDefault();
                    }
                    break;
            }
        }
    };
}