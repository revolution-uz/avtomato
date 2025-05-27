<template>
  <div class="flex relative">
    <select
      :value="selected"
      v-bind="defaultAttributes"
      @change="handleChange"
      class="w-full block form-control form-control-bordered form-input h-8 text-xs"
      ref="selectControl"
    >
      <option
        v-for="option in optionsForFilter"
        :value="option.value"
        :selected="isSelected(option)"
        :disabled="isDisabled(option)"
      >
        {{ labelFor(option) }}
      </option>
    </select>

    <IconArrow class="pointer-events-none absolute text-gray-700 right-[11px] top-[13px]"/>
  </div>
</template>

<script>
import omit from 'lodash/omit'
import map from 'lodash/map'
import IconArrow from "./IconArrow.vue";

export default {
  components: {IconArrow},

  emits: ['change'],

  inheritAttrs: false,

  props: {
    hasError: {type: Boolean, default: false},
    label: {default: 'label'},
    options: {type: Array, default: []},
    disabled: {type: Boolean, default: false},
    selected: {},
    isNullable: {type: Boolean, default: false}
  },

  methods: {
    labelFor(option) {
      return this.label instanceof Function
        ? this.label(option)
        : option[this.label]
    },
    isSelected(option) {
      return option.value === this.selected
    },
    isDisabled(option) {
      return option.disabled === true
    },
    handleChange(event) {
      let value = event?.target?.value ?? event
      this.$emit('change', value !== '-' ? value : null)
    }
  },

  computed: {
    defaultAttributes() {
      return omit(this.$attrs, ['class'])
    },
    optionsForFilter() {
      if (this.isNullable) {
        return [
          {value: '-', label: '-'},
          ...map(this.options, option => {
            return {value: option.value, label: option.label}
          })
        ]
      }

      return map(this.options, option => {
        return {value: option.value, label: option.label}
      })
    }
  },
}
</script>
