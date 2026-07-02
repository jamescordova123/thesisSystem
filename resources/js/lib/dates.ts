/**
 * Format an ISO date string (Y-m-d) for display as dd/mm/yyyy.
 */
export function formatDateDisplay(isoDate: string): string {
    if (!isoDate) {
        return '';
    }

    const [year, month, day] = isoDate.split('-');

    if (!year || !month || !day) {
        return '';
    }

    return `${day}/${month}/${year}`;
}
