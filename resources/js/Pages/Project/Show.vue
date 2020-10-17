<template>
  <app-layout>
    <template #header>
      <div class="flex justify-between items-end w-full">
        <p class="text-sm text-gray font-normal">
          <a :href="route('projects.index')"
             class="text-sm text-gray font-normal no-underline">My Projects</a> / {{ project.title }}
        </p>
        <a :href="route('projects.create')" class="button">Add Project</a>
      </div>
    </template>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="lg:flex -mx-3">
        <div class="lg:w-3/4 px-3">
          <div class="mb-8">
            <h2 class="text-gray font-normal text-lg mb-3">Tasks</h2>
            <!--tasks-->
            <div class="card mb-3"
                 v-for="task in project.tasks">{{ task.body }}
            </div>
          </div>
          <div>
            <h2 class="text-gray font-normal text-lg">General Notes</h2>
            <!--General Notes-->
            <textarea
                id="general_note"
                name="general_note"
                class="card w-full"
                style="height: 200px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquam commodi conse</textarea>
          </div>
        </div>

        <div class="lg:w-1/4 px-3 mb-8">
          <card :project="project" style="height: 200px;"/>
          <!--          <div class="card">-->
          <!--            <h1>{{ project.title }}</h1>-->
          <!--            <p v-html="project.description"></p>-->
          <!--            <a :href="route('projects.index')"-->
          <!--               class="inline-flex items-center px-4 py-2 bg-white-800 border border-blue-500 border-transparent rounded-md font-semibold text-xs text-blue-500 hover:text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:shadow-outline-blue transition ease-in-out duration-150"-->
          <!--            >Back</a>-->
          <!--          </div>-->
        </div>
      </div>
    </main>
  </app-layout>
</template>
<script>
import AppLayout from './../../Layouts/AppLayout'
import Card from './../../Pages/Project/Card'

export default {
  props: ['project'],
  components: {
    AppLayout,
    Card,
  },
}
</script>

<style scoped>

</style>