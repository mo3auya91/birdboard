<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Project</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg rounded px-8 pt-6 pb-8 mb-4 flex flex-col my-2">
                    <form method="post" @submit.prevent="createProject">
                        <div class="-mx-3 md:flex mb-6" v-for="locale in $i18n.availableLocales">
                            <div class="md:w-full px-3">
                                <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2"
                                       :for="`title[${locale}]`"
                                >{{ $t('app.title') + ' ' + $t(`app.${locale}`) }}</label>
                                <input v-model="form.title[locale]"
                                       class="block w-full bg-grey-lighter text-grey-darker border border-grey-lighter rounded py-3 px-4 mb-3"
                                       :id="`title[${locale}]`" type="text" placeholder="project title">
                                <p v-if="errors.hasOwnProperty(`title.${locale}`)" class="text-red text-xs italic">
                                    {{ errors.valueOf(`title.${locale}`)[`title.${locale}`][0] }}</p>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6" v-for="locale in $i18n.availableLocales">
                            <div class="md:w-full px-3">
                                <label class="block uppercase tracking-wide text-grey-darker text-xs font-bold mb-2"
                                       :for="`description[${locale}]`">{{
                                        $t('app.description') + ' ' + $t(`app.${locale}`)
                                    }}</label>
                                <textarea v-model="form.description[locale]" :id="`description[${locale}]`" cols="30"
                                          rows="10"
                                          :placeholder="$t('app.description')"
                                          class="block w-full bg-grey-lighter text-grey-darker border border-grey-lighter rounded py-3 px-4 mb-3"></textarea>
                                <p v-if="errors.hasOwnProperty(`description.${locale}`)"
                                   class="text-red text-xs italic">
                                    {{ errors.valueOf(`description.${locale}`)[`description.${locale}`][0] }}</p>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6 md:items-center">
                            <div class="md:w-1/3">
                                <button
                                    class="shadow bg-teal-400 hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                                    type="submit">
                                    {{ $t('app.submit') }}
                                </button>
                            </div>
                            <div class="md:w-2/3">
                                <inertia-link :href="route('projects.index')"
                                              class="shadow bg-teal-400 hover:bg-teal-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                                >{{ $t('app.cancel') }}
                                </inertia-link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
import AppLayout from './../../Layouts/AppLayout'

export default {
    props: {
        errors: Object
    },
    data() {
        return {
            form: {
                title: {
                    "ar": null,
                    "en": null,
                },
                description: {
                    "ar": null,
                    "en": null,
                },
                // title: null,
                //description: null,
            },
        }
    },
    components: {
        AppLayout,
    },
    methods: {
        createProject() {
            this.$inertia.post(route('projects.store'), this.form)
        },
    },
}
</script>
