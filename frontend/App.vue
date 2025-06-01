<template>
  <div class="min-h-screen bg-gray-900 text-white p-6">
    <header class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-green-400">Track Manager</h1>
      <button
        @click="openModal"
        class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-full flex items-center"
      >
        <i class="fas fa-plus mr-2"></i> Create New Track
      </button>
    </header>

    <TrackList
      :tracks="tracks"
      @edit="openModalWithTrack"
      @refresh="fetchTracks"
      @deleted="fetchTracks"
    />

    <TrackModal ref="trackModalRef" @saved="onSaved" />

    <div
      v-if="showToast"
      class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center animate-bounce"
    >
      <i class="fas fa-check-circle mr-2"></i>
      <span>{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import TrackList from "@/components/TrackList.vue";
import TrackModal from "@/components/TrackModal.vue";

axios.defaults.baseURL = "http://localhost:8000/api";

const tracks = ref([]);
const toastMessage = ref("");
const showToast = ref(false);
const trackModalRef = ref(null);

const fetchTracks = async () => {
  const response = await axios.get("/tracks");
  tracks.value = response.data;
};

const openModal = () => {
  trackModalRef.value.open();
};

const openModalWithTrack = (track) => {
  trackModalRef.value.open(track);
};

const onSaved = (message) => {
  toastMessage.value = message;
  showToast.value = true;
  fetchTracks();
  setTimeout(() => {
    showToast.value = false;
  }, 3000);
};

onMounted(fetchTracks);
</script>
