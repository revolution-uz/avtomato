<template>
  <div>
    <loader v-if="!commandData"></loader>
    <div v-if="commandData">
      <heading class="mb-6">{{ __("Command") }}: {{ commandData.name }}</heading>
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-3 py-2 px-6">
        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0">
          <div class="md:w-1/4 md:py-3"><h4 class="font-bold md:font-normal"><span>{{ __("Name") }}</span></h4></div>
          <div class="md:w-3/4 md:py-3 break-words"><p class="text-90 flex items-center">{{ commandData.name }}</p>
          </div>
        </div>

        <div
          class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0 border-t border-gray-100 dark:border-gray-700">
          <div class="md:w-1/4 md:py-3"><h4 class="font-bold md:font-normal"><span>{{ __("Description") }}</span></h4>
          </div>
          <div class="md:w-3/4 md:py-3 break-words"><p class="text-90 flex items-center">{{ commandData.description }}</p></div>
        </div>

        <div v-for="argument in commandData.arguments">
          <div v-if="!argument.hidden"
               class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0 border-t border-gray-100 dark:border-gray-700">
            <div class="md:w-1/4 md:py-3">
              <label :for="'id_argument_' + argument.name" class="inline-block text-80 pt-2 leading-tight">
                {{ argument.name }} <span v-if="argument.isRequired" class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:w-3/4 md:py-3 break-words">
              <input type="text" class="w-full form-control form-input form-input-bordered"
                     v-model="argument.value"
                     :id="'id_argument_' + argument.name"
                     :required="argument.isRequired"
                     :title="argument.description"
                     :name="argument.name"
                     :placeholder="argument.description">
            </div>
          </div>
        </div>
        <div v-for="option in commandData.options">
          <div v-if="!option.hidden"
               class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0 border-t border-gray-100 dark:border-gray-700">
            <div class="md:w-1/4 md:py-3">
              <label :for="'id_option_' + option.name" class="inline-block text-80 pt-2 leading-tight">
                {{ option.name }} <span v-if="option.isRequired" class="text-red-500 text-sm">*</span>
              </label>
            </div>
            <div class="md:w-3/4 md:py-3 break-words">
              <input v-model="option.value"
                     :id="'id_option_' + option.name"
                     :class="option.acceptValue === false ? 'checkbox' : 'form-control form-input form-input-bordered'"
                     :type="option.acceptValue === false ? 'checkbox' : 'text'"
                     :required="option.isRequired"
                     :name="option.name"
                     :title="option.description"
                     :placeholder="option.description">
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center md:py-8">
        <Link href="/nova-vendor/console-commands" class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6">
          {{ __("Cancel") }}
        </Link>
        <a @click="processCommand" size="md"
           class="cursor-pointer flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring
          bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex
          items-center font-bold px-4 h-9 text-sm flex-shrink-0">
          {{ __("Execute command") }}
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import {Localization} from 'laravel-nova'

export default {
  mixins: [Localization],
  metaInfo() {
    return {
      title: 'Command: ' + this.commandData['name'],
    }
  },
  props: {
    commandData: Array,
  },
  methods: {
    validateForm() {
      let areArgumentsValid = this.validateGroupFields(this.commandData.arguments);
      let areOptionsValid = this.validateGroupFields(this.commandData.options);

      return areArgumentsValid && areOptionsValid;
    },
    validateGroupFields(fields) {
      let isValid = true;

      fields.forEach((field) => {
        if (field.isRequired && field.value === null) {
          Nova.error(`The field: '${field.name}' can not be blank`);
          isValid = false;
        }
      });

      return isValid;
    },
    processCommand() {
      if (!this.validateForm()) {
        return false;
      }

      Nova.request()
        .post('/nova-vendor/console-commands/' + this.commandData['name'], this.commandData)
        .then(result => {
          Nova.success(`You've successfully requested execution of the command ${this.commandData['name']}.`);
        }).catch(reason => {
        Nova.error(reason);
      });
    }
  }
}
</script>
