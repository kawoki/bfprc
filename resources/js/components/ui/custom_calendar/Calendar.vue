<script lang="ts" setup>
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import { CalendarRoot, type CalendarRootEmits, type CalendarRootProps, useForwardPropsEmits } from 'reka-ui'
import { cn } from '@/lib/utils'
import { CalendarCell, CalendarCellTrigger, CalendarGrid, CalendarGridBody, CalendarGridHead, CalendarGridRow, CalendarHeadCell, CalendarHeader, CalendarHeading, CalendarNextButton, CalendarPrevButton } from '.'

const props = defineProps<CalendarRootProps & { 
    class?: HTMLAttributes['class']
    min?: string
    'disabled-dates'?: (date: Date) => boolean
}>()

const emits = defineEmits<CalendarRootEmits>()

const delegatedProps = reactiveOmit(props, 'class', 'min', 'disabled-dates')

const forwarded = useForwardPropsEmits(delegatedProps, emits)

// Add function to check if a date should be disabled
const isDateDisabled = (date: Date) => {
    if (props.min) {
        const minDate = new Date(props.min);
        minDate.setHours(0, 0, 0, 0);
        const currentDate = new Date(date);
        currentDate.setHours(0, 0, 0, 0);
        if (currentDate < minDate) return true;
    }
    
    if (props['disabled-dates']) {
        return props['disabled-dates'](date);
    }
    
    return false;
};
</script>

<template>
  <CalendarRoot
    v-slot="{ grid, weekDays }"
    data-slot="calendar"
    :class="cn('p-3', props.class)"
    v-bind="forwarded"
  >
    <CalendarHeader>
      <CalendarHeading />

      <div class="flex items-center gap-1">
        <CalendarPrevButton />
        <CalendarNextButton />
      </div>
    </CalendarHeader>

    <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
      <CalendarGrid v-for="month in grid" :key="month.value.toString()">
        <CalendarGridHead>
          <CalendarGridRow>
            <CalendarHeadCell
              v-for="day in weekDays" 
              :key="day"
              class="w-full"
            >
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>
        <CalendarGridBody>
          <CalendarGridRow 
            v-for="(weekDates, index) in month.rows" 
            :key="`weekDate-${index}`" 
            class="mt-2 w-full"
          >
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
              class="grow"
              :disabled="isDateDisabled(weekDate)"
            >
              <CalendarCellTrigger
                :day="weekDate"
                :month="month.value"
                :disabled="isDateDisabled(weekDate)"
              />
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </div>
  </CalendarRoot>
</template>

<style scoped>
.calendar-cell[data-disabled] {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
