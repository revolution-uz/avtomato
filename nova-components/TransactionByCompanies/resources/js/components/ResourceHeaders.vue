<template>
  <div>
    <div class="grid text-gray-500 text-xxs" style="grid-template-columns: 40px repeat(6, 1fr);">
      <span
        v-for="header in headers"
        class="text-left px-2 whitespace-nowrap uppercase tracking-wide py-2"
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
        'Name',
        'Status',
        'Atmos with card',
        'Atmos with pinfl',
        'Unired with pinfl',
        'Other',
      ],
    }
  },
}
</script>
