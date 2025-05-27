<template>
  <div class="grid group bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700"
       style="grid-template-columns: 60px repeat(6, 1fr);"
  >
    <span class="px-2 py-2 flex justify-center items-center">{{ resource.id }}</span>
    <span class="ml-3 px-2 py-2 flex items-center">{{ resource.installment_id }}</span>
    <span class="px-2 py-2 flex items-center">{{ resource.branch }}</span>
    <span class="px-2 py-2 flex items-center">{{ resource.transaction_type }}</span>
    <span class="px-2 py-2 flex items-center">{{ resource.amount }}</span>
    <span class="px-2 py-2 flex items-center">{{ resource.paid_at }}</span>
    <span class="px-2 py-2 flex items-center">{{ resource.status }}</span>
  </div>
</template>

<script>
export default {
  props: {
    resource: {type: Object, required: true},
  },
}
</script>
