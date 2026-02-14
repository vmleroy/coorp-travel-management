import type { AxiosError } from 'axios'

export interface ValidationError {
  errors?: Record<string, string[]>
  message?: string
}

export const formatValidationErrors = (errors: Record<string, string[]>): string => {
  return Object.entries(errors)
    .map(([_, messages]) => messages.join(', '))
    .join('\n')
}

export const getErrorMessage = (error: unknown, defaultMessage: string = 'Ocorreu um erro. Tente novamente.'): string => {
  const axiosError = error as AxiosError<ValidationError>
  const responseData = axiosError.response?.data

  if (responseData?.errors) {
    return formatValidationErrors(responseData.errors)
  }

  return responseData?.message || defaultMessage
}
