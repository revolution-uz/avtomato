<template>
  <div
    class="grid group bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700"
    style="grid-template-columns: 60px repeat(6, 1fr);"
    :class="{'cursor-pointer': cursor}"
    @click="visitResource"
  >
    <span class="px-2 py-4 flex justify-center items-center">{{ resource.id }}</span>
    <span class="px-2 py-4 flex items-center">{{ resource.name }}</span>
    <span class="px-2 py-4 flex items-center">{{ resource.status }}</span>
    <span class="px-2 py-4 flex items-center">{{ resource.atmos_with_card }}</span>
    <span class="px-2 py-4 flex items-center">{{ resource.atmos_with_pinfl }}</span>
    <span class="px-2 py-4 flex items-center">{{ resource.unired_with_pinfl }}</span>
    <span class="px-2 py-4 flex items-center">{{ resource.other }}</span>
  </div>
</template>

<script>
export default {
  props: {
    resource: {type: Object, required: true},
    cursor: {type: Boolean, default: true},
  },
  methods: {
    visitResource() {
      Nova.visit(`/transaction-by-companies/${this.resource.id}`);
    }
  },
}
</script>
