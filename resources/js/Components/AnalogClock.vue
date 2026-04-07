<template>
    <div class="flex items-center space-x-3">
        <!-- Analog soat -->
        <div class="relative w-10 h-10 flex-shrink-0">
            <!-- Doira -->
            <div class="absolute inset-0 border border-gray-200 rounded-full bg-gray-50"></div>

            <!-- Belgilar (12 ta) -->
            <div class="absolute inset-1">
                <div v-for="i in 12" :key="i"
                     class="absolute bg-gray-400"
                     :class="(i - 1) % 3 === 0 ? 'w-px h-1.5' : 'w-px h-1'"
                     :style="{
                         top: '0',
                         left: '50%',
                         transformOrigin: '50% 17px',
                         transform: `translateX(-50%) rotate(${(i - 1) * 30}deg)`
                     }"/>
            </div>

            <!-- Sekund strelkasi (qizil) -->
            <div class="absolute rounded-full bg-red-500"
                 style="width:1px; height:16px; top:50%; left:50%;
                        transform-origin: 50% bottom; z-index:1;"
                 :style="{ transform: `translateX(-50%) translateY(-100%) rotate(${secondDeg}deg)` }"/>

            <!-- Daqiqa strelkasi (ko'k) -->
            <div class="absolute rounded-full bg-blue-500"
                 style="width:1.5px; height:14px; top:50%; left:50%;
                        transform-origin: 50% bottom; z-index:2;"
                 :style="{ transform: `translateX(-50%) translateY(-100%) rotate(${minuteDeg}deg)` }"/>

            <!-- Soat strelkasi (qora) -->
            <div class="absolute rounded-full bg-gray-700"
                 style="width:2px; height:10px; top:50%; left:50%;
                        transform-origin: 50% bottom; z-index:3;"
                 :style="{ transform: `translateX(-50%) translateY(-100%) rotate(${hourDeg}deg)` }"/>

            <!-- Markaz nuqtasi -->
            <div class="absolute w-1.5 h-1.5 bg-gray-700 rounded-full z-10"
                 style="top:50%; left:50%; transform: translate(-50%,-50%)"/>
        </div>

        <!-- Raqamli vaqt va sana -->
        <div class="hidden lg:flex flex-col leading-tight">
            <div class="text-sm font-semibold text-gray-800 font-mono tracking-tight">
                <span>{{ hh }}</span>:<span>{{ mm }}</span>:<span class="text-red-500">{{ ss }}</span>
            </div>
            <div class="text-xs font-mono">
                <span class="text-blue-500 font-bold">{{ dd }}</span>
                <span class="text-gray-400 mx-0.5">/</span>
                <span class="text-green-500 font-bold">{{ mo }}</span>
                <span class="text-gray-400 mx-0.5">/</span>
                <span class="text-purple-500 font-bold">{{ yyyy }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const now = ref(new Date())
let timer = null

onMounted(() => { timer = setInterval(() => { now.value = new Date() }, 1000) })
onUnmounted(() => clearInterval(timer))

const pad  = (n) => String(n).padStart(2, '0')

const hh   = computed(() => pad(now.value.getHours()))
const mm   = computed(() => pad(now.value.getMinutes()))
const ss   = computed(() => pad(now.value.getSeconds()))
const dd   = computed(() => pad(now.value.getDate()))
const mo   = computed(() => pad(now.value.getMonth() + 1))
const yyyy = computed(() => now.value.getFullYear())

const secondDeg = computed(() => now.value.getSeconds() * 6)
const minuteDeg = computed(() => now.value.getMinutes() * 6)
const hourDeg   = computed(() => (now.value.getHours() % 12) * 30 + now.value.getMinutes() * 0.5)
</script>
