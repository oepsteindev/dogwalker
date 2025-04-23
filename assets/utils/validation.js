export function validateAppointment(date, time) {
  if (!date || !time) return false
  const appointmentDate = new Date(`${date}T${time}`)
  return appointmentDate > new Date() // Ensure future date
} 