<template>
  <div>
    <h2>All Tracks</h2>
    <table v-if="tracks.length">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Artist</th>
          <th>Duration</th>
          <th>ISRC</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="track in tracks" :key="track.id">
          <td>{{ track.id }}</td>
          <td>{{ track.title }}</td>
          <td>{{ track.artist }}</td>
          <td>{{ formatDuration(track.duration) }}</td>
          <td>{{ track.isrc || "-" }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else>No tracks found.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const tracks = ref([]);

const fetchTracks = async () => {
  try {
    const res = await axios.get("http://localhost:8000/api/tracks");
    tracks.value = res.data;
  } catch (err) {
    console.error("Failed to fetch tracks:", err);
  }
};

const formatDuration = (seconds) => {
  const m = Math.floor(seconds / 60)
    .toString()
    .padStart(2, "0");
  const s = (seconds % 60).toString().padStart(2, "0");
  return `${m}:${s}`;
};

onMounted(fetchTracks);
</script>
