<template>
  <div class="min-w-screen min-h-screen absolute top-0 left-0 overflow-hidden">
    <HeaderBar />
    <router-view />
  </div>
</template>

<script>
import HeaderBar from './components/HeaderBar.vue';

export default {
  components: {
    HeaderBar
  }
}
</script>
