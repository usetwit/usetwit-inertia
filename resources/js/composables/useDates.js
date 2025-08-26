import { computed, ref } from 'vue'
import { DateTime } from 'luxon'

export default function useDates(initialDate, numberOfMonths = 1) {

    const year = ref(initialDate.year)
    const month = ref(initialDate.month)
    const monthTexts = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    const dayTexts = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']
    const minYear = ref(1900)
    const maxYear = ref(2099)

    const months = computed(() => {
        const monthsList = []
        let startMonth = month.value
        let startYear = year.value

        for (let i = 0; i < numberOfMonths; i++) {
            monthsList.push({ month: startMonth, year: startYear, dates: makeMonth(startYear, startMonth) })

            startMonth++
            if (startMonth === 13) {
                startMonth = 1
                startYear++
            }
        }

        return monthsList
    })

    const makeMonth = (year, month) => {
        const dates = []

        const firstOfMonth = DateTime.utc(year, month, 1)
        let start = firstOfMonth.startOf('week')
        let end = firstOfMonth.endOf('month').endOf('week')
        const diffInDays = Math.round(end.diff(start, 'days').days)

        if (diffInDays < 42) {
            end = end.plus({ days: (42 - diffInDays) })
        }

        while (start <= end) {
            dates.push(start)
            start = start.plus({ days: 1 })
        }

        return dates
    }

    const isToday = day => DateTime.now().hasSame(day, 'day')

    const changeMonth = direction => {
        if (direction === 'increase' && year.value <= maxYear.value) {
            month.value = (month.value + 1 === 13) ? 1 : month.value + 1
            year.value += (month.value === 1) ? 1 : 0
        } else if (direction === 'decrease' && year.value >= minYear.value) {
            month.value = (month.value - 1 === 0) ? 12 : month.value - 1
            year.value -= (month.value === 12) ? 1 : 0
        }
    }

    const changeYear = (direction, step = 1) => {
        if (direction === 'increase' && year.value <= maxYear.value - step) {
            year.value += step
        } else if (direction === 'decrease' && year.value >= minYear.value + step) {
            year.value -= step
        }
    }

    const yearRounded = computed(() => Math.floor(year.value / 10) * 10)

    return {
        isToday,
        month,
        year,
        changeMonth,
        changeYear,
        yearRounded,
        months,
        monthTexts,
        dayTexts,
        minYear,
        maxYear,
    }
}
