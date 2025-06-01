<template>
  <div>
    <h2 class="text-xl font-bold mb-4 text-white">All Tracks</h2>

    <table
      v-if="tracks.length"
      class="w-full text-left bg-gray-800 text-gray-300 rounded shadow"
    >
      <thead>
        <tr class="bg-gray-700 text-green-400">
          <th class="px-6 py-4">ID</th>
          <th class="px-6 py-4 uppercase tracking-wider">Title</th>
          <th class="px-6 py-4 uppercase tracking-wider">Artist</th>
          <th class="px-6 py-4 uppercase tracking-wider">Duration</th>
          <th class="px-6 py-4 uppercase tracking-wider">ISRC</th>
          <th class="px-6 py-4 text-right uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-600">
        <tr
          v-for="track in tracks"
          :key="track.id"
          class="hover:bg-gray-700 transition"
        >
          <td class="px-6 py-4 font-medium">{{ track.id }}</td>
          <td class="px-6 py-4 font-medium">{{ track.title }}</td>
          <td class="px-6 py-4 font-medium">{{ track.artist }}</td>
          <td class="px-6 py-4 font-medium">
            {{ formatDuration(track.duration) }}
          </td>
          <td class="px-6 py-4 font-medium">{{ track.isrc || "-" }}</td>
          <td class="px-6 py-4 text-right">
            <button
              @click="editTrack(track)"
              class="mr-3 text-green-400 hover:text-green-300"
              title="Edit"
            >
              <i class="fas fa-edit"></i>
            </button>
            <button
              @click="deleteTrack(track.id)"
              class="text-red-400 hover:text-red-300"
              title="Delete"
            >
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else class="text-gray-400">No tracks found.</p>
  </div>
</template>

<script setup>
defineProps({
  tracks: Array,
});
import { ref, onMounted } from "vue";
import axios from "axios";
import TrackModal from "./TrackModal.vue";
import Swal from "sweetalert2";

const emit = defineEmits(["edit", "refresh"]);

const editTrack = (track) => {
  emit("edit", track);
};

const deleteTrack = async (id) => {
  const result = await Swal.fire({
    title: "Delete this track?",
    text: "This action cannot be undone!",
    icon: "warning",
    background: "#1f2937",
    color: "#e5e7eb",
    iconColor: "#facc15",
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#4b5563",
    confirmButtonText: "Yes, delete it!",
    showCancelButton: true,
    customClass: {
      popup: "rounded-lg shadow-2xl",
      confirmButton: "px-4 py-2 rounded bg-red-600 hover:bg-red-700",
      cancelButton: "px-4 py-2 rounded bg-gray-600 hover:bg-gray-700",
    },
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`http://localhost:8000/api/tracks/${id}`);
      emit("deleted");
      Swal.fire({
        title: "Deleted!",
        text: "Track has been deleted.",
        icon: "success",
        background: "#1e293b",
        color: "#f8fafc",
        iconColor: "#4ade80",
        confirmButtonText: "OK",
        confirmButtonColor: "#4ade80",
        customClass: {
          popup: "rounded-lg shadow-lg",
          title: "text-xl font-semibold",
          confirmButton:
            "px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white",
        },
      });
    } catch (err) {
      console.error("Failed to delete track:", err);
      Swal.fire({
        title: "Error!",
        text: `Failed to delete track with ID ${id}.`,
        icon: "error",
        background: "#1f2937",
        iconColor: "#f87171",
        confirmButtonText: "OK",
        confirmButtonColor: "#dc2626",
        customClass: {
          popup: "rounded-lg shadow-lg",
          title: "text-xl font-semibold",
          confirmButton:
            "px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white",
        },
      });
    }
  }
};

const formatDuration = (seconds) => {
  const m = Math.floor(seconds / 60)
    .toString()
    .padStart(2, "0");
  const s = (seconds % 60).toString().padStart(2, "0");
  return `${m}:${s}`;
};
</script>
