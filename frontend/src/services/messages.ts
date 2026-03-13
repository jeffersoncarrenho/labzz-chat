import { api } from "@/services/api";
import { Message } from "@/types/message";

interface MessagesResponse {
  conversation_id: number;
  page: number;
  limit: number;
  messages: Message[];
}

export async function getMessages(conversationId: number): Promise<Message[]> {
  const response = await api.get<MessagesResponse>(
    `/messages?conversation_id=${conversationId}`,
  );

  return response.data.messages;
}
