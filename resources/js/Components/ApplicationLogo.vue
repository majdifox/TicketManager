<template>
    <img 
        :src="logoSrc" 
        :alt="altText"
        :class="logoClasses"
    />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    class: {
        type: String,
        default: ''
    },
    src: {
        type: String,
        default: '/images/logo.png'
    },
    alt: {
        type: String,
        default: 'Logo'
    }
})

const logoSrc = props.src
const altText = props.alt
const logoClasses = props.class
</script>