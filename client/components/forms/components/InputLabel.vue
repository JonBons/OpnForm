<template>
  <label
    :for="nativeFor"
    class="input-label"
    :class="[
      theme.default.label,
      { 'uppercase text-xs': uppercaseLabels, 'text-sm/none': !uppercaseLabels },
      { 'sm:invisible': hideFieldNameDesktop, '': !hideFieldNameDesktop },
    ]"
  >
    <slot>
      {{ label }}
      <span
        v-if="required"
        class="text-red-500 required-dot"
      >*</span>
    </slot>
  </label>
</template>

<script>
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
export default {
  name: "InputLabel",

  props: {
    nativeFor: { type: String, default: null },
    theme: {
      type: Object, default: () => {
        const theme = inject("theme", null)
        if (theme) {
          return theme.value
        }
        return CachedDefaultTheme.getInstance()
      }
    },
    hideFieldNameDesktop: { type: Boolean, default: false },
    uppercaseLabels: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    label: { type: String, required: true },
  },
}
</script>
