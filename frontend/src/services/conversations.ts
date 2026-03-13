import { api } from "@/services/api";
import { Conversation } from "@/types/conversation";

export async function getConversations(
  userId: number,
): Promise<Conversation[]> {
  const response = await api.get(`/conversations?user_id=${userId}`);

  return response.data;
}
