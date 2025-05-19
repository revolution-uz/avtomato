<template>
  <Dropdown dusk="filter-selector" :should-close-on-blur="false">
    <Button
      :variant="hasAreAppliedFilters ? 'solid' : 'ghost'"
      dusk="filter-selector-button"
      icon="funnel"
      trailing-icon="chevron-down"
      padding="tight"
      :label="activeFilterCount > 0 ? activeFilterCount : ''"
      :aria-label="__('Filter Dropdown')"
    />

    <template #menu>
      <div
        class="select-none overflow-hidden bg-white dark:bg-gray-900 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 w-260"
      >
        <div class="bg-white dark:bg-gray-900 scroll-wrap overflow-x-hidden overflow-y-auto h-350">
          <div class="divide-y divide-gray-200 dark:divide-gray-800 divide-solid">
            <div v-if="filtersAreApplied" class="bg-gray-100">
              <button
                class="py-2 w-full block text-xs uppercase tracking-wide text-center text-gray-500 dark:bg-gray-800 dark:hover:bg-gray-700 font-bold focus:outline-none focus:text-primary-500"
                @click="handleClearSelectedFiltersClick"
              >
                {{ __('Reset Filters') }}
              </button>
            </div>

            <FilterContainer dusk="filter-company-status">
              <span>{{ __('Company status') }}</span>

              <template #filter>
                <SelectControl
                  v-model:selected="companyStatusValue"
                  :options="companyStatusOptions"
                  :is-nullable="true"
                  @change="companyStatusValue = $event"
                />
              </template>
            </FilterContainer>

            <FilterContainer dusk="filter-status">
              <span>{{ __('Status') }}</span>

              <template #filter>
                <SelectControl
                  v-model:selected="statusValue"
                  :options="statusOptions"
                  :is-nullable="true"
                  @change="statusValue = $event"
                />
              </template>
            </FilterContainer>

            <DateFilter filter-key="from" @change="fromValue = $event" :value="filter.from"/>
            <DateFilter filter-key="to" @change="toValue = $event" :value="filter.to"/>
          </div>
        </div>
      </div>
    </template>
  </Dropdown>
</template>

<script>
import {Button} from 'laravel-nova-ui'
import FilterContainer from "./FilterContainer.vue";
import SelectControl from "./SelectControl.vue";
import DateFilter from "./DateFilter.vue";

export default {
  components: {DateFilter, SelectControl, FilterContainer, Button},

  data() {
    return {
      companyStatusOptions: [
        {label: 'Created', value: 0},
        {label: 'Active', value: 1},
        {label: 'Inactive', value: -1},
      ],
      statusOptions: [
        {label: 'Success', value: 1},
        {label: 'Canceled', value: 0},
      ],
    }
  },

  emits: [
    'filter-changed',
    'clear-selected-filters',
  ],

  props: {
    filter: {type: Object, required: true},
    filtersAreApplied: {type: Boolean, default: true},
    // activeFilterCount: Number,
  },

  methods: {
    handleClearSelectedFiltersClick() {
      setTimeout(() => {
        this.$emit('clear-selected-filters')
      }, 500)
    },
  },

  computed: {
    statusValue: {
      set(event) {
        let value = event?.target?.value || event
        if (value === '-') {
          value = null
        }
        this.$emit('filter-changed', {filter: 'status', value: value})
      },
      get() {
        return this.filter.status ?? '-'
      },
    },
    companyStatusValue: {
      set(event) {
        let value = event?.target?.value || event
        this.$emit('filter-changed', {filter: 'company_status', value: value})
      },
      get() {
        return this.filter.company_status ?? '-'
      },
    },
    fromValue: {
      set(event) {
        let value = event?.target?.value || event
        this.$emit('filter-changed', {filter: 'from', value: value})
      },
      get() {
        return this.filter.from
      },
    },
    toValue: {
      set(event) {
        let value = event?.target?.value || event
        this.$emit('filter-changed', {filter: 'to', value: value})
      },
      get() {
        return this.filter.to
      },
    },
    hasAreAppliedFilters: function () {
      return this.filter.status || this.filter.company_status || this.filter.from || this.filter.to
    },
  },
}
</script>
