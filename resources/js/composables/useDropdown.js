import { nextTick, onBeforeUnmount, onMounted, ref, useTemplateRef, watch } from 'vue'

export default function useDropdown(positionX = 'left', positionY = 'bottom', minWidthParent = false, gap = 2) {
    const inputRef = ref()
    const dropdownRef = useTemplateRef('dropdownRef')
    const buttonRef = useTemplateRef('buttonRef')
    const showDropdown = ref(false)
    const dropdownStyle = ref({
        left: 'auto',
        right: 'auto',
        top: 'auto',
        bottom: 'auto',
    })

    const updateDropdownPosition = () => {
        if (inputRef.value instanceof HTMLElement && dropdownRef.value instanceof HTMLElement) {
            requestAnimationFrame(() => {
                const inputRect = inputRef.value.getBoundingClientRect()
                const dropdownRect = dropdownRef.value.getBoundingClientRect()

                const viewportHeight = window.innerHeight
                const viewportWidth = window.innerWidth

                const top = inputRect.top + window.scrollY
                const bottom = inputRect.bottom + window.scrollY
                const left = inputRect.left + window.scrollX
                const right = inputRect.right + window.scrollX

                const spaceBelow = viewportHeight - inputRect.bottom
                const spaceAbove = inputRect.top

                const exceedsViewport = dropdownRect.width > viewportWidth
                const fitsFromRight = inputRect.right >= dropdownRect.width
                const fitsFromLeft = viewportWidth - inputRect.left >= dropdownRect.width
                const offViewportToLeft = inputRect.left < 0
                const offViewportToRight = inputRect.right > viewportWidth

                if(minWidthParent) {
                    dropdownStyle.value.minWidth = `${inputRect.width}px`
                }

                if (positionX === 'right') {
                    if (exceedsViewport) {
                        setDropdownPositionX('auto', '0')
                    } else if (fitsFromRight) {
                        if (offViewportToRight) {
                            setDropdownPositionX('auto', '0')
                        } else {
                            setDropdownPositionX(`${right - dropdownRect.width}px`)
                        }
                    } else if (fitsFromLeft) {
                        if (offViewportToLeft) {
                            setDropdownPositionX('0')
                        } else {
                            setDropdownPositionX(`${left}px`)
                        }
                    } else {
                        setDropdownPositionX('auto', '0')
                    }
                } else if (positionX === 'left') {
                    if (exceedsViewport) {
                        setDropdownPositionX('0')
                    } else if (fitsFromLeft) {
                        if (offViewportToLeft) {
                            setDropdownPositionX('0')
                        } else {
                            setDropdownPositionX(`${left}px`)
                        }
                    } else if (fitsFromRight) {
                        if (offViewportToRight) {
                            setDropdownPositionX('auto', '0')
                        } else {
                            setDropdownPositionX(`${right - dropdownRect.width}px`)
                        }
                    } else {
                        setDropdownPositionX('0')
                    }
                } else {
                    const inputCenter = inputRect.left + inputRect.width / 2
                    const dropdownCenter = dropdownRect.width / 2

                    if (exceedsViewport) {
                        setDropdownPositionX('0')
                    } else if (inputCenter + dropdownCenter > viewportWidth) {
                        setDropdownPositionX('auto', '0')
                    } else if (inputCenter < dropdownCenter) {
                        setDropdownPositionX('0')
                    } else {
                        setDropdownPositionX(`${inputCenter - dropdownCenter}px`)
                    }
                }

                if (positionY === 'bottom') {
                    if (spaceBelow >= dropdownRect.height + gap) {
                        setDropdownPositionY(`${bottom + gap}px`)
                    } else if (spaceAbove >= dropdownRect.height + gap) {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    } else {
                        setDropdownPositionY(`${bottom + gap}px`)
                    }
                } else {
                    if (spaceAbove >= dropdownRect.height + gap) {
                        setDropdownPositionY('auto', `${viewportHeight - top + gap}px`)
                    } else {
                        setDropdownPositionY(`${bottom + gap}px`)
                    }
                }
            })
        }
    }

    const setDropdownPositionY = (topValue, bottomValue = 'auto') => {
        dropdownStyle.value.top = topValue
        dropdownStyle.value.bottom = bottomValue
    }

    const setDropdownPositionX = (leftValue, rightValue = 'auto') => {
        dropdownStyle.value.left = leftValue
        dropdownStyle.value.right = rightValue
    }

    watch(showDropdown, async () => {
        if (showDropdown.value) {
            await nextTick(() => updateDropdownPosition())
        }
    })

    const toggleDropdown = () => {
        showDropdown.value = !showDropdown.value
    }

    const handleScroll = () => {
        if (showDropdown.value) {
            updateDropdownPosition()
        }
    }

    const handleResize = () => {
        showDropdown.value = false
    }

    const handleClickOutside = event => {
        if (
            !dropdownRef.value?.contains(event.target) &&
            !inputRef.value?.contains(event.target) &&
            !buttonRef.value?.contains(event.target)
        ) {
            showDropdown.value = false
        }
    }

    onMounted(() => {
        window.addEventListener('resize', handleResize)
        window.addEventListener('scroll', handleScroll)
        document.addEventListener('click', handleClickOutside)
        document.querySelector('main').addEventListener('scroll', handleScroll)
        document.querySelectorAll('main div').forEach(div => {
            if (window.getComputedStyle(div).overflowX === 'auto') {
                div.addEventListener('scroll', handleScroll)
            }
        })
    })

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize)
        window.removeEventListener('scroll', handleScroll)
        document.removeEventListener('click', handleClickOutside)
        document.querySelector('main').removeEventListener('scroll', handleScroll)
        document.querySelectorAll('main div').forEach(div => {
            if (window.getComputedStyle(div).overflowX === 'auto') {
                div.removeEventListener('scroll', handleScroll)
            }
        })
    })

    return {
        inputRef,
        dropdownStyle,
        showDropdown,
        toggleDropdown,
        updateDropdownPosition,
    }
}
