<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50"
  >
    <div class="bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-2xl">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-green-400">
          {{ isEditMode ? "Edit Track" : "Add New Track" }}
        </h2>
        <button @click="close" class="text-gray-400 hover:text-white">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <form @submit.prevent="submitForm" class="space-y-4" novalidate>
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-1"
            >Title *</label
          >
          <input
            v-model="form.title"
            type="text"
            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"
            :class="{ 'border-red-500': validationErrors.title }"
            required
          />
          <p v-if="validationErrors.title" class="text-sm text-red-400 mt-1">
            {{ validationErrors.title }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300 mb-1"
            >Artist *</label
          >
          <input
            v-model="form.artist"
            type="text"
            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"
            :class="{ 'border-red-500': validationErrors.artist }"
            required
          />
          <p v-if="validationErrors.artist" class="text-sm text-red-400 mt-1">
            {{ validationErrors.artist }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300 mb-1"
            >Duration (mm:ss) *</label
          >
          <input
            v-model="form.durationInput"
            @blur="formatDuration"
            @input="onDurationInput"
            maxlength="5"
            placeholder="mm:ss"
            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 duration-input"
            :class="{ 'border-red-500': validationErrors.duration }"
            required
          />
          <p v-if="validationErrors.duration" class="text-sm text-red-400 mt-1">
            {{ validationErrors.duration }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-300 mb-1"
            >ISRC</label
          >
          <input
            v-model="form.isrc"
            type="text"
            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"
            :class="{ 'border-red-500': validationErrors.isrc }"
            placeholder="US-ABC-99-12345"
            pattern="[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}"
          />

          <p class="text-sm text-gray-400 mt-1">Format: XX-XXX-00-00000</p>
          <p v-if="validationErrors.isrc" class="text-sm text-red-400 mt-1">
            {{ validationErrors.isrc }}
          </p>
        </div>

        <div class="pt-2 flex justify-end space-x-3">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white transition"
          >
            {{ isEditMode ? "Update" : "Add" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { onMounted, onBeforeUnmount } from "vue";

const emitFn = defineEmits(["saved"]);

const isVisible = ref(false);
const isEditMode = ref(false);
const trackId = ref(null);

const form = ref({
  title: "",
  artist: "",
  duration: "",
  durationInput: "",
  isrc: "",
});

const open = (track = null) => {
  isVisible.value = true;
  isEditMode.value = !!track;

  if (track) {
    trackId.value = track.id;
    const formattedDuration = formatSecondsToDuration(track.duration);
    form.value = {
      title: track.title,
      artist: track.artist,
      duration: formattedDuration,
      durationInput: formattedDuration,
      isrc: track.isrc,
    };
  } else {
    trackId.value = null;
    form.value = {
      title: "",
      artist: "",
      duration: "",
      durationInput: "",
      isrc: "",
    };
  }
};

const close = () => {
  isVisible.value = false;
};

function handleKeydown(event) {
  if (event.key === "Escape" && isVisible.value) {
    close();
  }
}

onMounted(() => {
  window.addEventListener("keydown", handleKeydown);
});

onBeforeUnmount(() => {
  window.removeEventListener("keydown", handleKeydown);
});

// Converts "03:30" → 210
function convertDurationToSeconds(durationStr) {
  if (
    !durationStr ||
    typeof durationStr !== "string" ||
    durationStr.trim() === ""
  ) {
    return null;
  }

  const parts = durationStr.split(":");
  if (parts.length !== 2) {
    return null;
  }

  const [minutes, seconds] = parts.map(Number);
  if (
    isNaN(minutes) ||
    isNaN(seconds) ||
    minutes < 0 ||
    seconds < 0 ||
    seconds >= 60
  ) {
    return null;
  }

  return minutes * 60 + seconds;
}

// Converts 210 → "03:30"
function formatSecondsToDuration(seconds) {
  const mins = Math.floor(seconds / 60)
    .toString()
    .padStart(2, "0");
  const secs = (seconds % 60).toString().padStart(2, "0");
  return `${mins}:${secs}`;
}

function onDurationInput(e) {
  let val = e.target.value;

  // Allow only digits and at most one colon
  const colonCount = (val.match(/:/g) || []).length;
  if (colonCount > 1) return;

  // Strip invalid characters (except one colon)
  val = val.replace(/[^0-9:]/g, "");

  form.value.durationInput = val;
}

function formatDuration() {
  let input = form.value.durationInput.trim();

  // If input is empty, clear both fields
  if (!input) {
    form.value.duration = "";
    form.value.durationInput = "";
    return;
  }

  let mm = 0;
  let ss = 0;

  if (input.includes(":")) {
    const [m, s] = input.split(":");
    mm = parseInt(m, 10);
    ss = parseInt(s, 10);
  } else {
    const digits = input.replace(/\D/g, "");
    if (digits.length === 0) {
      form.value.duration = "";
      form.value.durationInput = "";
      return;
    }
    const paddedDigits = digits.padStart(4, "0");
    mm = parseInt(paddedDigits.slice(0, 2), 10);
    ss = parseInt(paddedDigits.slice(2), 10);
  }

  // If invalid, clear both fields
  if (isNaN(mm) || isNaN(ss) || mm > 59 || ss > 59 || mm < 0 || ss < 0) {
    form.value.duration = "";
    form.value.durationInput = "";
    return;
  }

  const formatted = `${mm.toString().padStart(2, "0")}:${ss
    .toString()
    .padStart(2, "0")}`;
  form.value.duration = formatted;
  form.value.durationInput = formatted;
}
const validationErrors = ref({});

const submitForm = async () => {
  try {
    // Clear previous errors
    validationErrors.value = {};

    // Handle duration - don't convert empty/invalid durations
    let durationSeconds = null;
    if (form.value.duration && form.value.duration.trim() !== "") {
      durationSeconds = convertDurationToSeconds(form.value.duration);
      // Additional check: if conversion results in 0 and original wasn't "00:00", treat as invalid
      if (durationSeconds === 0 && form.value.duration !== "00:00") {
        durationSeconds = null;
      }
    }

    const payload = {
      title: form.value.title,
      artist: form.value.artist,
      duration: durationSeconds,
      isrc: form.value.isrc,
    };

    if (isEditMode.value) {
      await axios.put(
        `http://localhost:8000/api/tracks/${trackId.value}`,
        payload
      );
      emitFn("saved", "Track updated successfully!");
    } else {
      await axios.post("http://localhost:8000/api/tracks", payload);
      emitFn("saved", "Track added successfully!");
    }

    close();
  } catch (error) {
    console.error("❌ Submit error:", error);

    // Handle validation errors
    if (error.response?.status === 400 && error.response.data?.errors) {
      validationErrors.value = error.response.data.errors;
      console.error("🚨 Validation errors:", error.response.data.errors);
    } else {
      // Handle other errors (network, server, etc.)
      alert("An error occurred while saving the track. Please try again.");
    }
  }
};

defineExpose({ open });
</script>

<style scoped>
.duration-input {
  font-family: monospace;
  letter-spacing: 1px;
}
</style>
