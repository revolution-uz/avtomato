<template>
  <div>
    <Head title="Transaction By Companies"/>

    <Heading class="mb-6">Transaction By Companies</Heading>

    <Card>
      <div v-if="resources.length > 0">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <ResourceTableToolbar
            :filter="filter"
            @filter-changed="value => filterChanged(value)"
            @clear-selected-filters="clearSelectedFilters"
          />
          <div class="overflow-hidden overflow-x-auto relative">
            <div class="w-full table-default">
              <ResourceHeaders/>
              <ResourceRow v-for="resource in resources" :resource="resource"/>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="relative bg-white dark:bg-gray-800 rounded-lg shadow">
        <ResourceTableToolbar
          :filter="filter"
          @filter-changed="value => filterChanged(value)"
          @clear-selected-filters="clearSelectedFilters"
        />
        <div class="flex flex-col justify-center items-center px-6 py-8">
          <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            No resources found.
          </p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script>
import ResourceTableToolbar from "../components/ResourceTableToolbar.vue";
import ResourceRow from "../components/ResourceRow.vue";
import ResourceHeaders from "../components/ResourceHeaders.vue";

export default {
  components: {ResourceHeaders, ResourceRow, ResourceTableToolbar},

  data() {
    return {
      resources: [],
      filter: {
        company_status: null,
        status: null,
        from: null,
        to: null,
      },
    }
  },

  mounted() {
    this.filter.company_status = this.getAttributeFromURL('company_status');
    this.filter.status = this.getAttributeFromURL('status');
    this.filter.from = this.getAttributeFromURL('from');
    this.filter.to = this.getAttributeFromURL('to');

    this.refreshResource();
  },

  methods: {
    filterChanged(value) {
      this.filter[value.filter] = value.value
      this.updateURL(value.filter, value.value);
      this.refreshResource();
    },
    clearSelectedFilters() {
      this.filter = {
        company_status: null,
        status: null,
        from: null,
        to: null,
      }
      for (const key in this.filter) {
        this.removeURLParameter(key);
      }
      this.refreshResource();
    },
    updateURL(attribute, value) {
      const url = new URL(window.location.href);
      url.searchParams.set(attribute, value);
      window.history.pushState({}, '', url);
    },
    getAttributeFromURL(attribute) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(attribute);
    },
    removeURLParameter(attribute) {
      const url = new URL(window.location.href);
      url.searchParams.delete(attribute);
      window.history.pushState({}, '', url);
    },
    refreshResource() {
      Nova.request().get('/nova-vendor/transaction-by-companies', {
        params: {
          company_status: this.filter.company_status === 'null' ? null : this.filter.company_status,
          status: this.filter.status === 'null' ? null : this.filter.status,
          from: this.filter.from === 'null' ? null : this.filter.from,
          to: this.filter.to === 'null' ? null : this.filter.to,
        }
      })
        .then(response => {
          this.resources = response.data;
        });
    },
  },
}
</script>
