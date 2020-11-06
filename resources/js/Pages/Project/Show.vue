<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between items-end w-full">
                <p class="text-sm text-gray font-normal">
                    <inertia-link :href="route('projects.index')" class="text-sm text-gray font-normal no-underline"
                    >{{ $t('app.my_projects') }}
                    </inertia-link>
                    {{ '/ ' + project.title[$i18n.locale] }}
                </p>
                <div class="flex item-center">
                    <img v-for="member in project.members" :src="member.profile_photo_url" :alt="member.name"
                         class="rounded-full w-8 mr-2">
                    <img :src="project.owner.profile_photo_url" :alt="project.owner.name" class="rounded-full w-8 mr-2">

                    <inertia-link :href="route('projects.edit',{'project':project.id})" class="button ml-6"
                    >{{ $t('app.edit_project') }}
                    </inertia-link>
                </div>
            </div>
        </template>
        <!--<inertia-link :href="route('projects.show', {'project':project.id})" id="reload-btn" class="hidden">reload</inertia-link>-->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:flex -mx-3">
                <div class="lg:w-3/4 px-3">
                    <div class="mb-8">
                        <h2 class="text-gray font-normal text-lg mb-3">Tasks</h2>
                        <!--tasks-->
                        <div class="card mb-3" v-for="task in project.tasks" :key="task.id">
                            <form method="post" @submit.prevent="updateTask(task.id)" :id="'update_task_'+task.id">
                                <div class="flex">
                                    <input type="text" class="w-full" name="body" :value="task.body"
                                           :id="`task_${task.id}_body`"
                                           :class="task.is_completed ? 'text-gray-400 italic' : ''">
                                    <input type="checkbox" name="is_completed" :checked="task.is_completed"
                                           v-on:change="updateTask(task.id)">
                                </div>
                            </form>
                        </div>

                        <div class="card mb-3">
                            <form method="post" @submit.prevent="createTask" id="createTaskForm">
                                <input type="text" v-model="form.body" placeholder="add new task"
                                       class="w-full py-2 px-1 text-lg">
                                <div v-if="errors.body" class="text-red text-xs italic">{{ errors.body[0] }}</div>
                            </form>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-gray font-normal text-lg">General Notes</h2>
                        <!--General Notes-->
                        <!--<form @submit.prevent="updateProject">-->
                        <textarea
                            id="general_note"
                            name="notes"
                            class="card w-full mb-4" v-model="notes"
                            style="height: 200px;" v-html="notes"></textarea>
                        <inertia-link :href="route('projects.update',{'project':project.id})"
                                      method="patch" :data="{
              notes:notes,
              title:project.title,
              description:project.description
            }"
                                      class="button">Update
                        </inertia-link>
                        <!--</form>-->
                    </div>
                </div>

                <div class="lg:w-1/4 px-3 mb-8">
                    <card :project="project" style="height: 200px;"/>
                    <!--<div class="card">-->
                    <!--  <h1>{{ project.title }}</h1>-->
                    <!--  <p v-html="project.description"></p>-->
                    <!--  <a :href="route('projects.index')"-->
                    <!--     class="inline-flex items-center px-4 py-2 bg-white-800 border border-blue-500 border-transparent rounded-md font-semibold text-xs text-blue-500 hover:text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:shadow-outline-blue transition ease-in-out duration-150"-->
                    <!--  >Back</a>-->
                    <!--</div>-->
                    <div class="card mt-3">
                        <!--<h3 class="font-normal text-xl py-4 -ml-5 mb-3 border-l-4 border-blue-light pl-4">-->
                        <!--  <inertia-link :href="route('projects.show', {'project':project.id})"-->
                        <!--                class="text-black no-underline">{{ project.title }}-->
                        <!--  </inertia-link>-->
                        <!--</h3>-->
                        <ul class="text-xs list-reset">
                            <li
                                :class="index === project.activities.length - 1 ? '' : 'mb-1'"
                                v-for="(activity,index) in project.activities">{{ activity.description }}
                                <span class="text-gray">{{ moment(activity.created_at).fromNow() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </app-layout>
</template>
<script>
import AppLayout from './../../Layouts/AppLayout'
import Card from './../../Pages/Project/Card'
import moment from 'moment'

export default {
    props: {
        errors: Object,
        project: Object
    },
    components: {
        AppLayout,
        Card,
    },
    data() {
        return {
            notes: this.project.notes,
            moment: moment,
            form: {
                body: null,
            },
        }
    },
    methods: {
        createTask() {
            axios.post(route('projects.tasks.store', {'project': this.project.id}),
                {'body': this.form.body},
                {headers: {'accept': 'application/json'}}
            ).then(response => {
                //todo show success message toast
                let tasks = response.data.tasks
                this.project.tasks.push(tasks[tasks.length - 1])
                this.project.activities = response.data.activities
                this.form.body = null
            }).catch(error => {
                //todo show error message toast
                console.log(error)
            })
        },
        updateTask(id) {
            let _form = document.getElementById('update_task_' + id)
            let form = new FormData(_form)
            let data = {
                'body': form.get('body'),
                'is_completed': form.get('is_completed'),
            }
            axios.patch(route('projects.tasks.update', {'project': this.project.id, 'task': id}),
                {
                    'body': form.get('body'),
                    'is_completed': form.get('is_completed'),
                },
                {headers: {'accept': 'application/json'}},
            ).then((response) => {
                //todo show success message toast
                // this.project = response.data
                let item = this.project.tasks.find((item) => {
                    return parseInt(item.id) === parseInt(id)
                })
                item.body = data.body
                item.is_completed = data.is_completed
                this.project.activities = response.data.activities
                document.getElementById(`task_${id}_body`).blur()
                //document.getElementById('reload-btn').click()
            }).catch(error => {
                //todo show error message toast
                console.log(error)
            })
        }
    },
}
</script>

<style scoped>

</style>
