import { inputTimePickerComponent } from "./input";
import { Slot } from "./slot";
import { SlotsManager } from "./slot-manager";

const FORMAT_12_HOUR = '12-hour';
const FORMAT_24_HOUR = '24-hour';

/**
 * Alpine data component for an accessible, fully-featured time picker.
 *
 * Supports 12/24-hour formats, locale-aware display, interval control, min/max constraints,
 * unavailable time ranges, special slot tagging, and multiple selection modes.
 *
 * Integrates with Livewire (via wire:model) and Alpine (x-model).
 *
 * @param {Object} options - Configuration object
 * @param {string} [options.model] - Livewire property name (e.g., 'time' for wire:model="time")
 * @param {Object} [options.livewire] - Livewire instance reference
 * @param {boolean} [options.isLive] - Use live model binding if true
 * @param {number} [options.interval=30] - Minutes between each selectable slot
 * @param {string|null} [options.min=null] - Minimum time as 'H:i' or 'now'
 * @param {string|null} [options.max=null] - Maximum time as 'H:i' or 'now'
 * @param {boolean} [options.isMultiple=false] - Allow multiple selection
 * @param {string|null} [options.unavailable=null] - Comma-separated unavailable times/ranges
 * @param {string|null} [options.openTo=null] - Initial scroll position as 'H:i'
 * @param {string} [options.locale='auto'] - BCP-47 locale for formatting
 * @param {string} [options.timeFormat='auto'] - Format: 'auto', '12-hour', or '24-hour'
 * @param {string} [options.triggerType='button'] - Trigger UI: 'button', 'input', or 'pills'
 * @param {string} [options.placeholder] - Text shown when no selection
 * @param {boolean} [options.openPanel] - Show time slots panel (false for input-only mode)
 * @param {Object} [options.special={}] - Special slot tags. Format: { tagKey: 'H:i,H:i-H:i' }
 *
 * @returns {Object} Alpine component data object
 * @example
 * // In Blade view with Alpine:
 */
const timePickerComponent = ({
    model,
    livewire,
    isLive,
    interval = 30,
    min = null,
    max = null,
    isMultiple,
    unavailable = null,
    openTo = null,
    locale = 'auto',
    timeFormat = 'auto',
    triggerType = 'button',
    placeholder,
    openPanel,
    special
}) => {

    /**
     * Setup Livewire $entangle binding for two-way sync.
     * @param {string} prop - Property name to entangle
     * @param {boolean} live - Use live modifier if true
     * @returns {*} Entangled binding
     */
    const $entangle = (prop, live) => {
        const binding = livewire.$entangle(prop);
        return live ? binding.live : binding;
    };

    /**
     * Initialize state from model property.
     * @param {string} model - Model property name
     * @param {boolean} live - Use live modifier
     * @returns {*} Initial state value
     */
    const $initState = (model, live) => model ? $entangle(model, live) : null;

    const resolvedLocale = locale === 'auto'
        ? (typeof navigator !== 'undefined' ? navigator.language : 'en-US')
        : locale;

    const is12Hour = timeFormat === FORMAT_12_HOUR
        ? true : timeFormat === FORMAT_24_HOUR ? false
            : SlotsManager.localeUses12Hour(resolvedLocale);

    return {
        // ────────────────────────────────────
        // INTERNAL STATE
        // ────────────────────────────────────
        /** @type {*} - Bound value from wire:model or x-model */
        __state: $initState(model, isLive),

        /** @type {boolean} - Whether the dropdown panel is open */
        __isOpen: false,

        /** @type {Slot[]} - All available time slots */
        __slots: [],

        /** @type {SlotsManager} - Manager for slot generation and parsing */
        __slotManager: new SlotsManager({ interval, min, max, unavailable, locale, timeFormat, special }),

        /** @type {Set<string>} - Currently selected time values */
        __selected: new Set(),

        /** @type {boolean} - Whether to use 12-hour format */
        __is12Hour: is12Hour,

        __inputTriggerEl: undefined,


        /**
         * Initialize component: generate slots, setup watchers, and keyboard navigation.
         * Runs after Alpine mounts the component.
         */
        init() {
            this.__slots = this.__slotManager.generate();


            this.$nextTick(() => {
                const external = this.$root?._x_model?.get();

                if (external !== undefined && external !== null) {
                    this.__state = external;
                }

                // the inputTrigger on the input belongs to the input scope (child), so we can't use $refs here
                // using querySelector is fine since we imperatively fill the triggers from parent
                // look inside time-picker/input.js for more context 
                if (this.isInput) this.__inputTriggerEl = this.$root.querySelector('[x-ref=inputTrigger]');

                Alpine.effect(() => {
                    if (this.__state !== undefined) {

                        this.selectedFromState();
                        this.updateTriggerDisplay();

                        if (this.isInput && this.__inputTriggerEl) {

                            const inputDataStack = Alpine.$data(this.__inputTriggerEl);

                            inputDataStack.fillTriggers(this.__state);
                        }
                    }

                    this.$root?._x_model?.set(this.__state);

                });
            });

            if (openPanel) this.setupRover();
        },

        /**
         * Sync selections from the bound state into the internal __selected Set.
         * Filters out disabled values.
         */
        selectedFromState() {
            const disabledValues = new Set(this.__slots.filter(s => s.disabled).map(s => s.value));

            const selected = this.__slotManager
                .values(this.__state || '')
                .map(s => s.trim())
                .filter(s => s && !disabledValues.has(s));

            this.__selected = new Set(selected);
        },

        /**
         * Generate the bound state value from the current selection Set.
         * Returns comma-separated times for multiple mode, single value for single mode.
         *
         * @returns {string|null} Formatted state value or null if empty
         */
        stateFromSelected() {
            return [...this.__selected].join(',') || null;
        },

        /**
         * Format a raw time value using locale and format settings.
         *
         * @param {string} value - Raw time string (e.g., '09:30')
         * @returns {string} Formatted time (e.g., '9:30 AM', '09:30 ')
         */
        formatSlotValue(value) {
            const mins = SlotsManager.parse(value);
            return mins !== null ? SlotsManager.format(mins, locale, timeFormat) : value;
        },

        /**
         * Check if a time value is currently selected.
         *
         * @param {string} value - Time value to check
         * @returns {boolean}
         */
        isSelected(value) {
            return this.__selected.has(value);
        },

        /**
         * Handle selection of a time slot.
         * In multiple mode, toggles its selected state. In single mode, selects and closes.
         *
         * @param {string} value - Time value to select
         * @param {boolean} [disabled=false] - Skip selection if disabled
         */
        handleSelect(value, disabled = false) {

            if (disabled) return;

            if (isMultiple) {
                if (this.__selected.has(value)) {
                    this.__selected.delete(value);
                } else {
                    this.__selected.add(value);
                }

                this.__state = this.stateFromSelected();
            } else {
                this.__selected = new Set([value]);
                this.__state = value;
                this.close();
            }
        },

        /**
         * Remove a selected time (used by pills trigger type).
         *
         * @param {string} value - Time value to remove
         */
        removeChip(value) {
            this.__selected.delete(value);
            this.__state = this.stateFromSelected();
        },

        /**
         * Update the display text of the trigger element.
         * Shows placeholder if no selection, otherwise shows formatted label(s).
         */
        updateTriggerDisplay() {
            const el = this.$refs.triggerValue;
            if (!el) return;

            if (this.__selected.size === 0) {
                el.textContent = placeholder;
                return;
            }

            el.textContent = this.buildDisplayLabel();
        },

        /**
         * Build the human-readable label for the trigger display.
         * Joins multiple selections with ', ' separator.
         *
         * @returns {string}
         */
        buildDisplayLabel() {
            return [...this.__selected]
                .map(slot => this.formatSlotValue(slot))
                .join(', ');
        },

        /**
         * Get selected items as an array of {value, label} objects.
         *
         * @type {Array<{value: string, label: string}>}
         */
        get selectedItems() {
            return [...this.__selected].map(v => {
                return { value: v, label: this.formatSlotValue(v) };
            });
        },

        /**
         * Check if any time is currently selected.
         *
         * @type {boolean}
         */
        get hasValue() {
            return this.__selected.size > 0;
        },

        /**
         * Open the dropdown panel and activate keyboard navigation.
         * Focuses the options list and activates the first item.
         */
        open() {
            this.__isOpen = true;

            this.activateSlot();

            this.$nextTick(() => {
                if (triggerType !== 'input') {
                    this.$rover.options.focus();
                    this.$rover.activateFirst();
                }
            });
        },

        /**
         * Close the dropdown panel and deactivate keyboard navigation.
         */
        close() {
            this.__isOpen = false;

            if (openPanel) {
                this.$rover.deactivate();
                this.$rover.collection.reset();
            }
        },

        /**
         * Activate the appropriate time slot on open.
         * Scrolls to the resolved target: explicit openTo → first selected → nearest to now.
         * Defers to next paint for correct layout measurement.
         */
        activateSlot() {

            const targetMins = this.__slotManager.resolveOpenTo(openTo, this.firstSelected, this.__slots);

            const value = new Slot(targetMins, locale, timeFormat).value;

            // Defer activation until after Alpine DOM updates and next paint.
            // Rover handles scrolling internally, so we wait for layout to stabilize
            // before calling activate to ensure correct positioning.
            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    this.$rover.activate(value);
                });
            });
        },

        /**
         * Toggle the dropdown open/closed state (used by button trigger).
         */
        handleButtonClick() {
            this.__isOpen ? this.close() : this.open();
        },

        /**
         * Open the dropdown if not already open (used by input trigger).
         */
        handleInputClick() {
            if (!this.__isOpen) this.open();
        },

        /**
         * Close the dropdown if clicked outside the trigger.
         *
         * @param {Element} target - The clicked element
         */
        handleClickAway(target) {
            const trigger = this.$refs.trigger;
            if (trigger && trigger.contains(target)) return;
            this.close();
        },

        /**
         * Clear all selections and reset the component to empty state.
         */
        clear() {
            this.__selected.clear();

            this.__state = null;

            queueMicrotask(() => this.close());
        },

        /**
         * Check if the dropdown is currently open.
         * @type {boolean}
         */
        get isShown() { return this.__isOpen; },

        /**
         * Check if multiple selection is enabled.
         * @type {boolean}
         */
        get isMultiple() { return isMultiple; },

        /**
         * Check if the trigger type is 'button'.
         * @type {boolean}
         */
        get isButton() { return triggerType === 'button'; },

        /**
         * Check if the trigger type is 'input'.
         * @type {boolean}
         */
        get isInput() { return triggerType === 'input'; },

        /**
         * Check if the trigger type is 'pills'.
         * @type {boolean}
         */
        get isChips() { return triggerType === 'pills'; },

        /**
         * Get the first selected time value.
         * @type {string|undefined}
         */
        get firstSelected() {
            let iterator = this.__selected.values();

            let value = iterator.next().value;

            return value ? SlotsManager.parse(value) : null;
        },

        /**
         * Setup Rover keyboard navigation:
         * - Arrow keys to navigate slots
         * - Enter to select
         * - Escape to close
         * - Alphanumeric keys to jump to matching slots
         */
        setupRover() {
            const optionsManager = this.$rover.options;

            this.$rover.button.on('click', () => this.handleButtonClick());

            optionsManager.enableDefaultOptionsHandlers();

            optionsManager.on('keydown', (event, _el, activeTimeSlot) => {
                if (event.key.length === 1 && /^[a-zA-Z0-9]$/.test(event.key)) {
                    this.$rover.activateByKey(event.key);
                    return;
                }

                if (event.key === 'Enter' && activeTimeSlot !== undefined) {
                    this.handleSelect(activeTimeSlot);
                    this.isMultiple || this.close();
                }

                if (event.key === 'Escape') {
                    this.close();
                }
            });
        },
    };
};

Alpine.data('timePickerComponent', timePickerComponent);
Alpine.data('inputTimePickerComponent', inputTimePickerComponent);