<template>
  <div>
    <Card v-if="companyResource" class="mb-6">
      <div class="w-full table-default">
        <CompanyResourceHeaders/>
        <CompanyResourceRow :resource="companyResource" :cursor="false"/>
      </div>
    </Card>

    <Head title="Transactions"/>

    <Heading class="mb-6">Transactions</Heading>

    <Card>
      <div v-if="resourceLoading" class="flex items-center justify-center z-30 p-6" style="min-height: 220px">
        <Loader class="text-gray-300"/>
      </div>
      <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <ResourceTableToolbar
          :filter="filter"
          @filter-changed="value => filterChanged(value)"
          @clear-selected-filters="clearSelectedFilters"
        />
        <div v-if="resources.length > 0" class="overflow-hidden overflow-x-auto relative">
          <div class="w-full table-default">
            <ResourceHeaders/>
            <ResourceRow v-for="resource in resources" :resource="resource"/>
          </div>
          <Pagination v-if="meta.total" :meta="meta" :transfer-count="resources.length"
                      :resource-loading="resourceLoading" @page-changed="pageChanged"/>
        </div>
        <div v-else class="flex flex-col justify-center items-center px-6 py-8">
          <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            No resources found.
          </p>
        </div>
      </div>
    </Card>
  </div>
</template>

<script>
import ResourceTableToolbar from "./../components/transactions/ResourceTableToolbar.vue";
import ResourceHeaders from "./../components/transactions/ResourceHeaders.vue";
import ResourceRow from "./../components/transactions/ResourceRow.vue";
import Pagination from "./../components/transactions/Pagination.vue";
import CompanyResourceHeaders from "./../components/ResourceHeaders.vue";
import CompanyResourceRow from "./../components/ResourceRow.vue";

export default {
  components: {
    ResourceRow,
    ResourceHeaders,
    ResourceTableToolbar,
    Pagination,
    CompanyResourceRow,
    CompanyResourceHeaders
  },

  props: {
    company_id: {type: Number, required: true},
  },

  data() {
    return {
      resources: [],
      meta: {
        page: 1,
        per_page: 25,
        total: null,
      },
      filter: {
        per_page: null,
        type: null,
        status: null,
        from: null,
        to: null,
      },
      resourceLoading: false,
      companyResource: null,
    }
  },

  mounted() {
    this.meta.page = this.getAttributeFromURL('page');
    this.filter.per_page = this.getAttributeFromURL('per_page');
    this.filter.type = this.getAttributeFromURL('type');
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
        per_page: null,
        type: null,
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
      this.resourceLoading = true;
      Nova.request().get(`/nova-vendor/transaction-by-companies/${this.company_id}`, {
        params: {
          type: this.filter.type === 'null' ? null : this.filter.type,
          status: this.filter.status === 'null' ? null : this.filter.status,
          from: this.filter.from === 'null' ? null : this.filter.from,
          to: this.filter.to === 'null' ? null : this.filter.to,
          per_page: this.filter.per_page === 'null' ? null : this.filter.per_page,
          page: this.meta.page,
        }
      })
        .then(response => {
          this.resources = response.data.resources;
          this.meta = response.data.meta;
          this.companyResource = response.data.company;
          this.resourceLoading = false;
        });
    },
    pageChanged(value) {
      this.meta.page = value
      this.updateURL('page', value);
      this.refreshResource();
    },
  },
  watch: {
    'meta.page'(newValue) {
      this.updateURL('page', newValue);
    }
  }
}
</script>
