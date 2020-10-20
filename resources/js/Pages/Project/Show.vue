<template>
  <app-layout>
    <template #header>
      <div class="flex justify-between items-end w-full">
        <p class="text-sm text-gray font-normal">
          <inertia-link :href="route('projects.index')" class="text-sm text-gray font-normal no-underline">My Projects
          </inertia-link>
          / {{ project.title }}
        </p>
        <a :href="route('projects.create')" class="button">Add Project</a>
      </div>
    </template>
    <inertia-link :href="route('projects.show', {'project':project.id})" id="reload-btn" class="hidden">reload
    </inertia-link>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="lg:flex -mx-3">
        <div class="lg:w-3/4 px-3">
          <div class="mb-8">
            <h2 class="text-gray font-normal text-lg mb-3">Tasks</h2>
            <!--tasks-->
            <div class="card mb-3" v-for="task in project.tasks" :key="task.id">
              <form method="post" @submit.prevent="updateTask(task.id,$event)" :id="'update_task_'+task.id">
                <div class="flex">
                  <input type="text" class="w-full" name="body" :value="task.body">
                  <input type="checkbox" name="is_completed" :checked="task.is_completed"
                         v-on:change="updateTask(task.id)">
                </div>
              </form>
            </div>

            <div class="card mb-3">
              <form method="post" @submit.prevent="createTask">
                <input type="text" v-model="form.body" placeholder="add new task"
                       class="w-full py-2 px-1 text-lg">
                <div v-if="errors.body" class="text-red text-xs italic">{{ errors.body[0] }}</div>
              </form>
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
  props: {
    errors: Object,
    updateErrors: Object,
    project: Object
  },
  components: {
    AppLayout,
    Card,
  },
  data() {
    return {
      selectedTask: null,
      form: {
        body: null,
        is_completed: null,
      },
      updateForm: {
        body: null,
        is_completed: null,
      },
    }
  },
  methods: {
    createTask() {
      this.$inertia.post(route('projects.tasks.store', {'project': this.project.id}), this.form)
    },
    updateTask(id, e) {
      let _form = document.getElementById('update_task_' + id)
      let form = new FormData(_form)
      axios.patch(route('projects.tasks.update', {'project': this.project.id, 'task': id}),
          {
            'body': form.get('body'),
            'is_completed': form.get('is_completed'),
          },
          {
            'accept': 'application/json',
          })
          .then(response => {
            //todo show success message toast
            document.getElementById('reload-btn').click()
          })
          .catch(error => {
            //todo show error message toast
            console.log(error)
          })
    },
    updateSelectedTask(id) {
      this.selectedTask = this.project.tasks.find((item) => {
        return parseInt(item.id) === parseInt(id)
      })
    }
  },
}
</script>

<style scoped>

</style>