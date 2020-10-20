<template>
  <app-layout>
    <template #header>
      <div class="flex justify-between items-end w-full">
        <h2 class="text-sm text-gray font-normal">My Projects</h2>
        <inertia-link :href="route('projects.create')" class="button">Add Project</inertia-link>
<!--        <a :href="route('projects.create')" class="button">Add Project</a>-->
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class=" overflow-hidden shadow-xl sm:rounded-lg">
          <div class="lg:flex lg:flex-wrap -mx-3">
            <div v-if="projects.length"
                 v-for="project in projects"
                 class="lg:w-1/3 px-3 pb-6"
            >
              <card :project="project" style="height: 200px;"/>
            </div>
            <div v-else>No projects</div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>
<script>
import AppLayout from './../../Layouts/AppLayout'
import Card from './../../Pages/Project/Card'

export default {
  props: ['projects'],
  components: {
    AppLayout,
    Card,
  },
}
</script>

<style scoped>

</style>