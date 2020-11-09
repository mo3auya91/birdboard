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
                        <form @submit.prevent="updateProject" id="update-project">
                        <textarea
                            id="general_note"
                            name="notes"
                            class="card w-full mb-4" v-model="notes"
                            style="height: 200px;" v-html="notes"></textarea>
                            <!--                        <inertia-link :href="route('projects.update',{'project':project.id})"-->
                            <!--                                      method="patch"-->
                            <!--                                      class="button"-->
                            <!--                                      :data="{-->
                            <!--                                                notes:notes,-->
                            <!--                                                title:project.title,-->
                            <!--                                                description:project.description-->
                            <!--                                             }"-->
                            <!--                        >{{ $t('app.update') }}-->
                            <!--                        </inertia-link>-->
                            <button type="submit" class="text-xs button">{{ $t('app.update') }}</button>
                        </form>
                    </div>
                </div>

                <div class="lg:w-1/4 px-3 mb-8">
                    <card :project="project" style="height: 200px;"/>
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
                    <div class="card flex flex-col mt-3">
                        <h3 class="font-normal text-xl py-4 -ml-5 mb-3 border-l-4 border-blue-light pl-4">
                            {{ $t('app.invite_user') }}
                        </h3>
                        <form method="post" id="invite-user" @submit.prevent="inviteUser">
                            <div class="mb-3">
                                <label for="email" class="hidden"></label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    required
                                    v-model="inviteUserForm.email"
                                    class="border border-grey-light rounded w-full py-2 px-3"
                                    :placeholder="$t('app.email_address')"
                                >
                                <span class="text-red-500 hidden" id="email-error"></span>
                            </div>
                            <button type="submit" class="text-xs button">{{ $t('app.invite') }}</button>
                        </form>
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
            inviteUserForm: {
                email: null,
            },
            // projectForm: {
            //     notes: this.project.notes,
            //     title: this.project.title,
            //     description: this.project.description
            // },
        }
    },
    methods: {
        createTask() {
            axios.post(route('projects.tasks.store', {'project': this.project.id}),
                {'body': this.form.body},
                {headers: {'accept': 'application/json'}}
            ).then(response => {
                let tasks = response.data.tasks
                this.project.tasks.push(tasks[tasks.length - 1])
                this.project.activities = response.data.activities
                this.form.body = null
                this.successToast(this.$t('app.created_successfully'))
            }).catch(error => {
                this.errorToast(error.response.data.message, error.response.data.errors)
            })
        },
        inviteUser() {
            let email_error = document.getElementById('email-error')
            axios.post(route('project.invitations', {'project': this.project.id}),
                {'email': this.inviteUserForm.email},
                {headers: {'accept': 'application/json'}}
            ).then(() => {
                //email_error.classList.add('hidden')
                this.successToast(this.$t('app.invitation_sent'))
            }).catch((errors) => {
                this.errorToast(errors.response.data.message, errors.response.data.errors)
                // email_error.innerText = errors.response.data.errors['email'][0]
                // email_error.classList.remove('hidden')
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
                data,
                {headers: {'accept': 'application/json'}},
            )
                .then((response) => {
                    let item = this.project.tasks.find((item) => {
                        return parseInt(item.id) === parseInt(id)
                    })
                    item.body = data.body
                    item.is_completed = data.is_completed
                    this.project.activities = response.data.activities
                    document.getElementById(`task_${id}_body`).blur()
                    // if (this.$page.flash.success) {
                    //     this.successToast(this.$page.flash.success)
                    this.successToast(this.$t('app.updated_successfully'))
                    // }
                })
                .catch(error => {
                    this.errorToast(error.response.data.message, error.response.data.errors)
                })
        },
        updateProject() {
            let data = {
                notes: this.notes,
                title: {
                    'ar': this.project.title['ar'],
                    'en': this.project.title['en'],
                },
                description: {
                    'ar': this.project.description['ar'],
                    'en': this.project.description['en'],
                }
            }
            // this.$inertia.visit(route('projects.update', {'project': this.project.id}), {
            //     method: 'patch',
            //     data: data,
            //     replace: false,
            //     preserveState: false,
            //     preserveScroll: false,
            //     only: [],
            //     headers: {},
            //     onCancelToken: cancelToken => {},
            //     onCancel: () => {},
            //     onBefore: visit => {},
            //     onStart: visit => {},
            //     onProgress: progress => {},
            //     onSuccess: page => {},
            //     onFinish: () => {
            //         this.successToast(this.$t('app.updated_successfully'))
            //     },
            // })
            axios.patch(route('projects.update', {'project': this.project.id}),
                data,
                {headers: {'accept': 'application/json'}},
            ).then((response) => {
                this.successToast(this.$t('app.updated_successfully'))
            }).catch(error => {
                this.errorToast(error.response.data.message, error.response.data.errors)
            })
        }
    },
    mounted() {
        //console.log(this.$page.flash.success)
        // console.log(this.flash.success)
        //console.log(this.$page.user.can('update', this.project))
    },
    // watch: {
    //     'this.$page.flash.success': function (newVal, oldVal) { // watch it
    //         console.log('Prop changed: ', newVal, ' | was: ', oldVal)
    //     },
    //     flash: {
    //         handler() {
    //             //console.log('watch')
    //             this.successToast(this.flash.success)
    //             this.my_prop = true
    //             //console.log(this.$page.flash.success)
    //         },
    //         deep: true,
    //     },
    // },
}
</script>

<style scoped>

</style>
