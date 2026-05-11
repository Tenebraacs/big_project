using System;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace SideQuestJar.Serialization
{
    /// <summary>Laravel/API often emits booleans as JSON numbers (0/1) or strings.</summary>
    public sealed class FlexibleBoolJsonConverter : JsonConverter<bool>
    {
        public override bool Read(ref Utf8JsonReader reader, Type typeToConvert, JsonSerializerOptions options)
        {
            return reader.TokenType switch
            {
                JsonTokenType.True => true,
                JsonTokenType.False => false,
                JsonTokenType.Number => reader.TryGetInt64(out var l) ? l != 0 : reader.GetDouble() != 0,
                JsonTokenType.String => ParseLoose(reader.GetString()),
                JsonTokenType.Null => false,
                _ => throw new JsonException($"Cannot convert {reader.TokenType} to bool.")
            };
        }

        private static bool ParseLoose(string? s)
        {
            if (string.IsNullOrWhiteSpace(s)) return false;
            if (bool.TryParse(s, out var b)) return b;
            return s.Trim() == "1";
        }

        public override void Write(Utf8JsonWriter writer, bool value, JsonSerializerOptions options)
        {
            writer.WriteRawValue(value ? "true" : "false");
        }
    }
}
