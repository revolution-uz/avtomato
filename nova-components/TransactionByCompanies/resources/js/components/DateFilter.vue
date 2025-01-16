<template>
    <FilterContainer>
        <span>{{ filterKey }}</span>

        <template #filter>
            <input
                class="w-full flex form-control h-8 text-xs form-input form-control-bordered"
                type="date"
                autocomplete="off"
                :value="value"
                @change="handleChange"
            />
        </template>
    </FilterContainer>
</template>

<script>
import FilterContainer from "./FilterContainer.vue";

export default {
    components: {FilterContainer},

    emits: ['change'],

    props: {
        filterKey: {
            type: String,
            required: true,
        },
        value: String,
    },

    methods: {
        handleChange(event) {
            let value = event?.target?.value ?? event
            this.$emit('change', value)
        },
    },
}
</script>
