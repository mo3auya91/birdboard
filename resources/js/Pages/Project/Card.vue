<template>
  <div class="card">
    <h3 class="font-normal text-xl py-4 -ml-5 mb-3 border-l-4 border-blue-light pl-4">
      <!--<a :href="'/projects/'+project.id" class="text-black no-underline">{{ project.title }}</a>-->
      <a :href="route('projects.show', {'project':project.id})" class="text-black no-underline">{{ project.title }}</a>
    </h3>
    <div class="truncate w-20 text-gray-description">{{ project.description }}</div>
  </div>
</template>

<script>
export default {
  props: ['project'],
  name: "Card"
}
</script>
