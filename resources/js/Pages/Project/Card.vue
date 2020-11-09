<template>
    <div class="card flex flex-col">
        <h3 class="font-normal text-xl py-4 -ml-5 mb-3 border-l-4 border-blue-light pl-4">
            <!--<a :href="'/projects/'+project.id" class="text-black no-underline">{{ project.title }}</a>-->
            <inertia-link :href="route('projects.show', {'project':project.id})"
                          class="text-black no-underline">{{ project.title[$i18n.locale] }}
            </inertia-link>
            <!--<a :href="route('projects.show', {'project':project.id})" class="text-black no-underline">{{ project.title }}</a>-->
        </h3>
        <!--<div class="truncate w-20 text-grey mb-4 flex-1">{{ project.description[$i18n.locale] }}</div>-->
        <div class="text-grey mb-4 flex-1">{{ project.description[$i18n.locale] }}</div>
        <footer class="text-right">
            <inertia-link :href="route('projects.destroy', {'project':project.id})" method="delete"
                          class="text-black no-underline text-xs ">{{ $t('app.delete') }}
            </inertia-link>
        </footer>
    </div>
</template>

<script>
export default {
    props: ['project'],
    name: "Card"
}
</script>
