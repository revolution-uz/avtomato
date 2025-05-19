<template>
  <div>
    <div class="grid text-gray-500 text-xxs" style="grid-template-columns: 60px repeat(6, 1fr);">
      <span
        v-for="header in headers"
        class="px-2 py-2 whitespace-nowrap uppercase tracking-wide"
        :class="[header === 'Id' ? 'text-center' : 'text-left', header === 'Installment id' ? 'ml-3' : '']"
      >
        {{ header }}
      </span>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        'Id',
        'Installment id',
        'Branch',
        'Transaction type',
        'Amount',
        'Paid at',
        'Status',
      ],
    }
  },
}
</script>
