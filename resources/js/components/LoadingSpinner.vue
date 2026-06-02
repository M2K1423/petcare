<template>
  <div :class="['flex justify-center items-center', containerClass]">
    <div :class="['animate-spin rounded-full border-b-2', spinnerClass]"></div>
  </div>
</template>

<script setup>
defineProps({
  containerClass: {
    type: String,
    default: 'py-20'
  },
  spinnerClass: {
    type: String,
    default: 'h-10 w-10 border-blue-600'
  }
})
</script>
