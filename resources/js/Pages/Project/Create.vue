<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Project
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg rounded px-8 pt-6 pb-8 mb-4 flex flex-col my-2">
          <div class="-mx-3 md:flex mb-6">
            <div class="md:w-full px-3">
              <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2"
                     for="title">
                Title
              </label>
              <input
                  class="block w-full bg-grey-lighter text-grey-darker border border-grey-lighter rounded py-3 px-4 mb-3"
                  id="title" type="text" placeholder="project title">
              <p class="text-grey-dark text-xs italic">validation error here</p>
            </div>
          </div>
          <div class="-mx-3 md:flex mb-6">
            <div class="md:w-full px-3">
              <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2"
                     for="description">
                Description
              </label>
              <textarea name="description" id="description" cols="30" rows="10" placeholder="description"
                        class="block w-full bg-grey-lighter text-grey-darker border border-grey-lighter rounded py-3 px-4 mb-3"></textarea>
              <p class="text-grey-dark text-xs italic">validation error here</p>
            </div>
          </div>
          <div class="-mx-3 md:flex mb-6 md:items-center">
            <div class="md:w-1/3">
              <button
                  class="shadow bg-teal-400 hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                  type="button">
                Send
              </button>
            </div>
            <div class="md:w-2/3">
              <a
                  class="shadow bg-teal-400 hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                  :href="route('projects.index')">
                Cancel
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>
<script>
import AppLayout from './../../Layouts/AppLayout'

export default {
  props: [],
  components: {
    AppLayout,
  },
}
</script>

<style scoped>

</style>