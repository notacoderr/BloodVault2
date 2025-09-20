import nodemailer from 'nodemailer';

const DEFAULT_FROM = process.env.MAIL_FROM_ADDRESS || 'no-reply@bloodvault.local';

function createTransporter() {
  if (process.env.MAIL_HOST) {
    return nodemailer.createTransport({
      host: process.env.MAIL_HOST,
      port: Number(process.env.MAIL_PORT || 587),
      secure: process.env.MAIL_ENCRYPTION === 'ssl',
      auth: process.env.MAIL_USERNAME
        ? {
            user: process.env.MAIL_USERNAME,
            pass: process.env.MAIL_PASSWORD
          }
        : undefined
    });
  }

  return nodemailer.createTransport({
    streamTransport: true,
    newline: 'unix',
    buffer: true
  });
}

export const transporter = createTransporter();

export async function sendMail({ to, subject, text, html, from = DEFAULT_FROM }) {
  await transporter.sendMail({ from, to, subject, text, html });
}

export default { transporter, sendMail };
