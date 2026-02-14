/**
 * Converte uma data local para string YYYY-MM-DD sem conversão de timezone
 * Usado para enviar datas ao backend mantendo a data local selecionada
 */
export function formatDateToString(date: Date): string {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

/**
 * Converte string de data (YYYY-MM-DD ou ISO timestamp) para formato dd/mm/yyyy
 * Usado para exibir datas vindas do backend (que estão em UTC) na timezone local
 */
export function formatDate(dateString: string): string {
  if (!dateString) return ''

  try {
    // Se for um timestamp ISO completo (ex: 2026-02-14T00:00:00.000000Z), pegar apenas a parte da data
    const datePart = dateString.includes('T') ? dateString.split('T')[0] : dateString

    if (!datePart) return 'Data inválida'

    const [year, month, day] = datePart.split('-')
    if (!year || !month || !day) return 'Data inválida'

    return `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`
  } catch (error) {
    return 'Data inválida'
  }
}

/**
 * Retorna a data de hoje às 00:00 (meia-noite) para usar como minDate em datepickers
 */
export function getTodayAtMidnight(): Date {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return today
}

/**
 * Retorna o label traduzido para um status
 */
export function statusLabel(status: string): string {
  switch (status) {
    case 'pending':
      return 'Pendente'
    case 'approved':
      return 'Aprovado'
    case 'rejected':
      return 'Rejeitado'
    case 'cancelled':
      return 'Cancelado'
    default:
      return status
  }
}

/**
 * Retorna a severidade (cor) para um status no PrimeVue Tag
 */
export function statusSeverity(status: string): string {
  switch (status) {
    case 'pending':
      return 'warning'
    case 'approved':
      return 'success'
    case 'rejected':
      return 'danger'
    case 'cancelled':
      return 'info'
    default:
      return ''
  }
}
