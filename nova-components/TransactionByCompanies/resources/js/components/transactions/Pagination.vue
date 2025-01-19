<template>
  <div class="rounded-b-lg border-t border-gray-100 dark:border-gray-700">
    <nav class="flex justify-between items-center">
      <!-- Previous Link -->
      <button
        :disabled="!hasPreviousPages || resourceLoading"
        class="text-xs font-bold py-3 px-4 focus:outline-none rounded-bl-lg focus:ring focus:ring-inset"
        :class="{
          'text-primary-500 hover:text-primary-400 active:text-primary-600': hasPreviousPages,
          'text-gray-300 dark:text-gray-600': !hasPreviousPages || resourceLoading,
        }"
        rel="prev"
        @click.prevent="selectPreviousPage"
      >
        {{ __('Previous') }}
      </button>

      {{ startOfCount + 1 }}-{{ startOfCount + transferCount }} of {{ meta.total }}

      <!-- Next Link -->
      <button
        :disabled="!hasMorePages || resourceLoading"
        class="text-xs font-bold py-3 px-4 focus:outline-none rounded-br-lg focus:ring focus:ring-inset"
        :class="{
                    'text-primary-500 hover:text-primary-400 active:text-primary-600': hasMorePages,
                    'text-gray-300 dark:text-gray-600': !hasMorePages || resourceLoading,
                }"
        rel="next"
        @click.prevent="selectNextPage"
      >
        {{ __('Next') }}
      </button>
    </nav>
  </div>
</template>

<script>
export default {
  emits: ['page-changed'],

  props: {
    meta: {type: Object},
    transferCount: Number,
    resourceLoading: {type: Boolean, default: false},
  },

  methods: {
    selectPreviousPage() {
      this.selectPage(Number(this.meta.page) - 1)
    },
    selectNextPage() {
      this.selectPage(Number(this.meta.page) + 1)
    },
    selectPage(page) {
      this.$emit('page-changed', page)
    },
  },

  computed: {
    hasPreviousPages: function () {
      return Number(this.meta.page) !== 1
    },
    hasMorePages: function () {
      return Number(this.meta.page) !== Math.ceil(this.meta.total / this.meta.per_page)
    },
    startOfCount: function () {
      return (Number(this.meta.page) - 1) * this.meta.per_page
    }
  },
}
</script>
